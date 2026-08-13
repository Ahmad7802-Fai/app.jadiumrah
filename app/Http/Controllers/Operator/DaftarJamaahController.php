<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Jamaah;
use App\Models\Keberangkatan;
use App\Services\JamaahService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\JamaahExport;
use Maatwebsite\Excel\Facades\Excel;
class DaftarJamaahController extends Controller
{
    protected JamaahService $service;

    public function __construct(JamaahService $service)
    {
        $this->service = $service;
    }

    /* ============================================================
     | INDEX
     | SUPERADMIN / OPERATOR → lihat semua
     ============================================================ */
    public function index(Request $r)
    {
        $query = Jamaah::with(['keberangkatan','paketMaster'])
            ->orderByDesc('created_at');

        if ($r->q) {
            $query->where(function ($q) use ($r) {
                $q->where('nama_lengkap','like',"%{$r->q}%")
                  ->orWhere('nik','like',"%{$r->q}%")
                  ->orWhere('no_id','like',"%{$r->q}%");
            });
        }

        if ($r->id_keberangkatan) {
            $query->where('id_keberangkatan', $r->id_keberangkatan);
        }

        $data = $query->paginate(15)->withQueryString();
        $keberangkatanList = Keberangkatan::orderBy('tanggal_berangkat')->get();

        return view('operator.daftar-jamaah.index', compact('data','keberangkatanList'));
    }


    /* ============================================================
     | CREATE
     ============================================================ */
    public function create()
    {
        $this->authorize('create', Jamaah::class);

        $autoNoID = $this->service->generateNoId();

        $keberangkatan = Keberangkatan::where('status','Aktif')
            ->orderBy('tanggal_berangkat')
            ->get();

        return view('operator.daftar-jamaah.create', compact(
            'autoNoID',
            'keberangkatan'
        ));
    }


    /* ============================================================
     | STORE — FULL SERVICE
     ============================================================ */    
    public function store(Request $request)
    {
        $validated = $request->validate([
            // WAJIB
            'id_keberangkatan'  => 'required|exists:keberangkatan,id',
            'nama_lengkap'      => 'required|string',
            'nama_ayah'         => 'required|string',
            'nik'               => 'required|string',
            'no_hp'             => 'required|string',
            'tempat_lahir'      => 'required|string',
            'tanggal_lahir'     => 'required|date',
            'status_pernikahan' => 'required|string',
            'jenis_kelamin'     => 'required|in:L,P',
            'tipe_kamar'        => 'required|in:quad,triple,double',
            'diskon'            => 'nullable|integer',
            'tipe_jamaah'       => 'required|in:reguler,tabungan,cicilan',

            // OPSIONAL
            'nama_passport'     => 'nullable|string',
            'nama_mahram'       => 'nullable|string',
            'status_mahram'     => 'nullable|string',

            // SCREENING
            'pernah_umroh'      => 'nullable|in:Ya,Tidak',
            'pernah_haji'       => 'nullable|in:Ya,Tidak',
            'merokok'           => 'nullable|in:Ya,Tidak',
            'penyakit_khusus'   => 'nullable|in:Ya,Tidak',
            'kursi_roda'        => 'nullable|in:Ya,Tidak',
            'nama_penyakit'     => 'nullable|string',

            // LAINNYA
            'foto'              => 'nullable|image|max:2048',
            'keterangan'        => 'nullable|string',
        ]);

        // 🔐 AUTORITATIF — NO ID & HARGA DITENTUKAN SERVICE
        $this->service->create($validated);

        return redirect()
            ->route('operator.daftar-jamaah.index')
            ->with('success', 'Jamaah berhasil disimpan');
    }




    /* ============================================================
     | EDIT
     ============================================================ */
    public function edit(int $id)
    {
        $jamaah = Jamaah::with(['payments' => function ($q) {
            $q->where('status', 'valid')
            ->where('is_deleted', 0);
        }])->findOrFail($id);

        $this->authorize('update', $jamaah);

        // FLAG: apakah sudah ada pembayaran
        $hasPayment = $jamaah->payments->isNotEmpty();

        // Keberangkatan:
        // - tetap tampilkan keberangkatan lama walau non-aktif
        $keberangkatan = Keberangkatan::where('status', 'Aktif')
            ->orWhere('id', $jamaah->id_keberangkatan)
            ->orderBy('tanggal_berangkat')
            ->get();

        return view('operator.daftar-jamaah.edit', compact(
            'jamaah',
            'keberangkatan',
            'hasPayment'
        ));
    }


    /* ============================================================
     | UPDATE — FULL SERVICE
     ============================================================ */
    public function update(Request $request, int $id)
    {
        /* =====================================================
        | AMBIL DATA & AUTH
        ===================================================== */
        $jamaah = Jamaah::findOrFail($id);
        $this->authorize('update', $jamaah);

        /* =====================================================
        | CEK PEMBAYARAN VALID
        ===================================================== */
        $hasPayment = $jamaah->payments()
            ->where('status', 'valid')
            ->where('is_deleted', 0)
            ->exists();

        /**
         * Jika sudah ada pembayaran:
         * - keberangkatan & tipe kamar TIDAK boleh berubah
         * - kita paksa pakai nilai lama
         */
        if ($hasPayment) {
            $request->merge([
                'id_keberangkatan' => $jamaah->id_keberangkatan,
                'tipe_kamar'       => $jamaah->tipe_kamar,
            ]);
        }

        /* =====================================================
        | VALIDATION RULES (WHITELIST WAJIB LENGKAP)
        ===================================================== */
        $rules = [
            // IDENTITAS WAJIB
            'nama_lengkap'      => 'required|string|max:150',
            'nama_ayah'         => 'required|string|max:150',
            'nik'               => 'required|string|max:50',
            'no_hp'             => 'required|string|max:20',
            'tempat_lahir'      => 'required|string|max:100',
            'tanggal_lahir'     => 'required|date',
            'status_pernikahan' => 'required|string',
            'jenis_kelamin'     => 'required|in:L,P',

            // OPSIONAL
            'nama_passport'     => 'nullable|string|max:150',
            'nama_mahram'       => 'nullable|string|max:150',
            'status_mahram'     => 'nullable|string|max:50',

            // SCREENING
            'pernah_umroh'      => 'nullable|in:Ya,Tidak',
            'pernah_haji'       => 'nullable|in:Ya,Tidak',
            'merokok'           => 'nullable|in:Ya,Tidak',
            'penyakit_khusus'   => 'nullable|in:Ya,Tidak',
            'kursi_roda'        => 'nullable|in:Ya,Tidak',
            'nama_penyakit'     => 'nullable|string|max:150',

            // LAINNYA
            'foto'              => 'nullable|image|max:2048',
            'keterangan'        => 'nullable|string',
        ];

        // ⛔ HANYA BOLEH UBAH TIPE JAMAAH JIKA BELUM ADA PAYMENT
        if (!$hasPayment) {
            $rules['tipe_jamaah'] = 'required|in:reguler,tabungan,cicilan';

            $rules += [
                'id_keberangkatan' => 'required|exists:keberangkatan,id',
                'tipe_kamar'       => 'required|in:quad,triple,double',
                'diskon'           => 'nullable|integer|min:0',
            ];
        }
        
        /* =====================================================
        | VALIDATE (FIELD YANG TIDAK DI SINI = DIABAIKAN)
        ===================================================== */
        $validated = $request->validate($rules);

        /* =====================================================
        | UPDATE VIA SERVICE (FIELD-AWARE)
        ===================================================== */
        $this->service->update($jamaah->id, $validated);

        /* =====================================================
        | REDIRECT
        ===================================================== */
        return redirect()
            ->route('operator.daftar-jamaah.index')
            ->with('success', 'Data jamaah berhasil diperbarui.');
    }

    /* ============================================================
     | DELETE
     ============================================================ */
    public function destroy(int $id)
    {
        $jamaah = Jamaah::findOrFail($id);
        $this->authorize('delete', $jamaah);

        try {
            $this->service->delete($jamaah->id);

            return back()->with('success','Jamaah berhasil dihapus.');

        } catch (\Throwable $e) {

            return back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /* ============================================================
     | AJAX — KEBERANGKATAN → PAKET MASTER
     ============================================================ */
    public function ajaxKeberangkatanPaket(int $id)
    {
        $keberangkatan = Keberangkatan::with('paketMaster')->findOrFail($id);

        if (! $keberangkatan->paketMaster) {
            return response()->json(['paket' => null]);
        }

        $p = $keberangkatan->paketMaster;

        return response()->json([
            'paket' => [
                'id'           => $p->id,
                'nama_paket'   => $p->nama_paket,
                'harga_quad'   => (int) $p->harga_quad,
                'harga_triple' => (int) $p->harga_triple,
                'harga_double' => (int) $p->harga_double,
            ]
        ]);
    }

    /* ============================================================
    | SHOW — DETAIL JAMAAH
    ============================================================ */
    public function show(int $id)
    {
        $jamaah = Jamaah::with([
                'keberangkatan',
                'paketMaster',
                'branch',
                'agent'
            ])
            ->findOrFail($id);

        $this->authorize('view', $jamaah);

        return view('operator.daftar-jamaah.show', [
            'jamaah' => $jamaah,
            'item'   => $jamaah, // alias
        ]);

    }
    /* ============================================================
    | PRINT — DETAIL JAMAAH
    ============================================================ */
    public function print(int $id)
    {
        $jamaah = Jamaah::with('keberangkatan')->findOrFail($id);

        $qrUrl = 'https://quickchart.io/qr?size=150&text='
       . urlencode(route('operator.daftar-jamaah.show', $jamaah->id));


        return Pdf::loadView(
            'operator.daftar-jamaah.print',
            compact('jamaah','qrUrl')
        )
        ->setPaper('A4','portrait')
        ->stream('jamaah-'.$jamaah->no_id.'.pdf');
    }
    /* ============================================================
    | EXPORT — DAFTAR JAMAAH PDF
    ============================================================ */
    public function exportPdf()
    {
        $jamaahs = Jamaah::with(['branch','agent','keberangkatan'])
            ->orderBy('nama_lengkap')
            ->get();

        $pdf = Pdf::loadView(
            'operator.daftar-jamaah.export-pdf',
            compact('jamaahs')
        )->setPaper('a4', 'landscape');

        return $pdf->download('daftar-jamaah.pdf');
    }
    /* ============================================================
    | EXPORT — DAFTAR JAMAAH EXCEL
    ============================================================ */
    public function exportExcel()
    {
        $jamaahs = Jamaah::with('keberangkatan')
            ->orderBy('nama_lengkap')
            ->get();

        return Excel::download(
            new JamaahExport($jamaahs),
            'daftar-jamaah.xlsx'
        );
    }
}

