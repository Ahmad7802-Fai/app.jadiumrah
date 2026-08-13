<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\PassportJamaah;
use App\Models\Jamaah;
use App\Models\SuratSrp;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class PassportJamaahController extends Controller
{
    /**
     * LIST + SEARCH + FILTER
     */
    public function index(Request $request)
    {
        $query = PassportJamaah::with('jamaah');

        // SEARCH (nama / no passport)
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_paspor', 'like', "%{$search}%")
                  ->orWhereHas('jamaah', function ($j) use ($search) {
                      $j->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }

        // FILTER rekomendasi
        if ($request->filter_rekomendasi) {
            $query->where('rekomendasi_paspor', $request->filter_rekomendasi);
        }

        $passports = $query->latest()->paginate(20);

        return view('operator.passport.index', compact('passports'));
    }

    /**
     * SHOW CREATE FORM
     */
    public function create()
    {
        $jamaah = Jamaah::orderBy('nama_lengkap', 'ASC')->get();
        return view('operator.passport.create', compact('jamaah'));
    }

    /**
     * STORE PASSPORT + SIMPAN SRP OTOMATIS
     */
    public function store(Request $request)
    {
        $request->validate([
            'jamaah_id' => 'required|exists:jamaah,id',
            'nomor_paspor' => 'nullable|string|max:50',
            'tanggal_terbit_paspor' => 'nullable|date',
            'tanggal_habis_paspor' => 'nullable|date',
            'tempat_terbit_paspor' => 'nullable|string|max:100',
            'negara_penerbit' => 'nullable|string|max:100',
            'alamat_lengkap' => 'nullable|string',
            'kecamatan' => 'nullable|string|max:100',
            'kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:20',
            'tujuan_imigrasi' => 'nullable|string|max:255',
        ]);

        // ===== AUTO REKOMENDASI =====
        $rekomendasi = null;
        if ($request->tanggal_habis_paspor) {
            $exp = Carbon::parse($request->tanggal_habis_paspor);
            $now = Carbon::now();
            $monthsLeft = $now->diffInMonths($exp, false);

            if ($monthsLeft >= 6) $rekomendasi = 'Masih Berlaku';
            elseif ($monthsLeft >= 1) $rekomendasi = 'Segera Perpanjang';
            else $rekomendasi = 'Perlu Perpanjang';
        }

        // SIMPAN PASSPORT
        $payload = array_merge(
            $request->only([
                'jamaah_id','nomor_paspor','tanggal_terbit_paspor','tanggal_habis_paspor',
                'tempat_terbit_paspor','negara_penerbit','alamat_lengkap','kecamatan','kota',
                'provinsi','kode_pos','tujuan_imigrasi'
            ]),
            ['rekomendasi_paspor' => $rekomendasi]
        );

        $passport = PassportJamaah::create($payload);

        // SIMPAN SRP OTOMATIS untuk jamaah ini (jika belum ada)
        $this->createSrpRecordIfNotExists($passport);

        return redirect()
            ->route('operator.passport.index')
            ->with('success', 'Data passport berhasil ditambahkan dan SRP disimpan.');
    }

    /**
     * EDIT FORM
     */
    public function edit($id)
    {
        $passport = PassportJamaah::with('jamaah')->findOrFail($id);
        return view('operator.passport.edit', compact('passport'));
    }

    /**
     * UPDATE PASSPORT (juga update rekomendasi)
     */
    public function update(Request $request, $id)
    {
        $passport = PassportJamaah::findOrFail($id);

        $request->validate([
            'nomor_paspor' => 'nullable|string|max:50',
            'tanggal_terbit_paspor' => 'nullable|date',
            'tanggal_habis_paspor' => 'nullable|date',
            'tempat_terbit_paspor' => 'nullable|string|max:100',
            'negara_penerbit' => 'nullable|string|max:100',
            'alamat_lengkap' => 'nullable|string',
            'kecamatan' => 'nullable|string|max:100',
            'kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:20',
            'tujuan_imigrasi' => 'nullable|string|max:255',
        ]);

        // UPDATE rekomendasi otomatis bila tanggal habis diisi
        $data = $request->only([
            'nomor_paspor','tanggal_terbit_paspor','tanggal_habis_paspor',
            'tempat_terbit_paspor','negara_penerbit','alamat_lengkap','kecamatan','kota',
            'provinsi','kode_pos','tujuan_imigrasi'
        ]);

        if (!empty($data['tanggal_habis_paspor'])) {
            $exp = Carbon::parse($data['tanggal_habis_paspor']);
            $monthsLeft = Carbon::now()->diffInMonths($exp, false);

            if ($monthsLeft >= 6) $data['rekomendasi_paspor'] = 'Masih Berlaku';
            elseif ($monthsLeft >= 1) $data['rekomendasi_paspor'] = 'Segera Perpanjang';
            else $data['rekomendasi_paspor'] = 'Perlu Perpanjang';
        }

        $passport->update($data);

        // Jika tujuan_imigrasi berubah, update record SRP (tetap pakai nomor lama)
        if ($passport->tujuan_imigrasi && $srp = SuratSrp::where('jamaah_id', $passport->jamaah_id)->first()) {
            // kita tidak ubah nomor_surat, hanya pastikan row ada -> nothing else
            // if needed, update timestamps
            if (property_exists($srp, 'updated_at')) {
                $srp->updated_at = now();
                $srp->save();
            }
        }

        return redirect()
            ->route('operator.passport.index')
            ->with('success', 'Data passport berhasil diperbarui.');
    }

    /**
     * CETAK SRP (PDF) — men-generate QR, watermark, ttd optional
     */
    public function srp($id)
    {
        $passport = PassportJamaah::with('jamaah')->findOrFail($id);

        // pastikan SRP record ada (jika belum, buat)
        $srp = SuratSrp::where('jamaah_id', $passport->jamaah_id)->first();
        if (!$srp) {
            $srp = $this->createSrpRecordIfNotExists($passport);
        }

        $nomorSurat = $srp->nomor_surat;

        // QR content: verifikasi URL (premium) + ringkasan text
        // Jika ingin QR berisi plain text, adjust $qrValue accordingly
        $verifyUrl = url("/verifikasi-srp?id={$srp->id}&token=" . $this->qrToken($srp));
        $qrValue = "SRP: {$passport->jamaah->nama_lengkap}\n".
                   "NIK: {$passport->jamaah->nik}\n".
                   "Nomor: {$nomorSurat}\n".
                   "Verifikasi: {$verifyUrl}";

        // generate base64 QR (90px untuk compact premium)
        $qrCode = base64_encode(
            QrCode::format('png')->size(90)->generate($qrValue)
        );

        // OPTIONAL: framed/stamp style (we handle in blade with CSS)
        // prepare ttd image path (if exists)
        $ttdPath = public_path('assets/ttd-direktur.png');
        $ttdExists = file_exists($ttdPath);

        // prepare watermark path
        $watermark = public_path('assets/logo-gsm.png');

        // tanggal format id
        Carbon::setLocale('id');
        $tanggalId = Carbon::now()->translatedFormat('d F Y');

        $pdf = PDF::loadView('operator.passport.srp', compact(
            'passport','nomorSurat','qrCode','verifyUrl','ttdExists','watermark','tanggalId'
        ))
        ->setPaper('A4','portrait');

        // stream file
        return $pdf->stream("SRP-{$passport->jamaah->nama_lengkap}.pdf");
    }

    /**
     * CETAK DETAIL PASSPORT (print passport)
     */
    public function print($id)
    {
        $passport = PassportJamaah::with('jamaah')->findOrFail($id);
        $pdf = PDF::loadView('operator.passport.print', compact('passport'))
            ->setPaper('A4','portrait');
        return $pdf->stream('passport-'.$passport->jamaah->nama_lengkap.'.pdf');
    }

    /**
     * Helper: buat record SRP bila belum ada. Mengembalikan model SuratSrp.
     *
     * NOTE: jika tabel surat_srp tidak punya updated_at, pastikan model SuratSrp memiliki:
     *   public $timestamps = false;
     *
     * @param PassportJamaah $passport
     * @return SuratSrp
     */
    private function createSrpRecordIfNotExists(PassportJamaah $passport)
    {
        $existing = SuratSrp::where('jamaah_id', $passport->jamaah_id)->first();
        if ($existing) return $existing;

        $nomor = $this->generateNomorSurat();

        // Jika model SuratSrp $timestamps = false, kami sertakan created_at manual
        $payload = [
            'jamaah_id' => $passport->jamaah_id,
            'nomor_surat' => $nomor,
        ];

        // Try create: if model expects timestamps it will set created_at automatically
        $srp = SuratSrp::create($payload);

        return $srp;
    }

    /**
     * Generate nomor surat profesional (reset tiap bulan) — format: 001/GSM/XII/2025
     */
    private function generateNomorSurat()
    {
        $bulanRomawiMap = [
            1=>"I","II","III","IV","V","VI","VII","VIII","IX","X","XI","XII"
        ];
        $bulanRomawi = $bulanRomawiMap[date('n')];
        $tahun = date('Y');

        // hitung jumlah surat pada bulan & tahun yang sama
        $countThisMonth = SuratSrp::whereYear('created_at', $tahun)
            ->whereMonth('created_at', date('m'))
            ->count();

        $nextNo = str_pad($countThisMonth + 1, 3, '0', STR_PAD_LEFT);

        return "{$nextNo}/GSM/{$bulanRomawi}/{$tahun}";
    }

    /**
     * Small helper to generate a short random token to embed in verification URL (optional)
     */
    private function qrToken(SuratSrp $srp)
    {
        // simple deterministic token: bisa disimpan ke DB jika perlu real verification
        return substr(hash('sha256', $srp->id . '|' . $srp->nomor_surat), 0, 12);
    }
}
