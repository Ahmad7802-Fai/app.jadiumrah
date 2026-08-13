<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Manifest;
use App\Models\Jamaah;
use App\Models\Keberangkatan;
use Barryvdh\DomPDF\Facade\Pdf;

class ManifestController extends Controller
{
    /**
     * INDEX PREMIUM
     * — Filter keberangkatan
     * — Search jamaah / kamar
     * — Statistik
     */
    public function index(Request $request)
    {
        // --- Ambil list keberangkatan untuk filter
        $keberangkatanList = Keberangkatan::orderBy('tanggal_berangkat','desc')->get();

        // --- Query awal manifest
        $query = Manifest::with(['jamaah','keberangkatan']);

        // Filter keberangkatan
        if ($request->keberangkatan_id) {
            $query->where('keberangkatan_id', $request->keberangkatan_id);
        }

        // Search
        if ($request->search) {
            $s = $request->search;
            $query->where(function($q) use ($s){
                $q->whereHas('jamaah', function($j) use ($s){
                    $j->where('nama_lengkap','like',"%{$s}%");
                })
                ->orWhere('nomor_kamar','like',"%{$s}%");
            });
        }

        // Result
        $manifests = $query->orderBy('tipe_kamar')->orderBy('nomor_kamar')->paginate(25);

        // --- Statistik
        $statQuery = Manifest::query();
        if ($request->keberangkatan_id) {
            $statQuery->where('keberangkatan_id', $request->keberangkatan_id);
        }

        $stat_total  = $statQuery->count();
        $stat_kamar  = $statQuery->distinct()->count('nomor_kamar');
        $stat_quad   = $statQuery->where('tipe_kamar','Quad')->count();
        $stat_triple = manifest::where('keberangkatan_id', $request->keberangkatan_id)
                        ->where('tipe_kamar','Triple')->count();
        $stat_double = manifest::where('keberangkatan_id', $request->keberangkatan_id)
                        ->where('tipe_kamar','Double')->count();

        return view('operator.manifest.index', compact(
            'manifests',
            'keberangkatanList',
            'stat_total',
            'stat_kamar',
            'stat_quad',
            'stat_triple',
            'stat_double'
        ));
    }


    /**
     * CREATE MODE CERDAS
     * — Pilih keberangkatan → tampilkan jamaah yang belum masuk manifest
     * — Auto nomor kamar
     */
    public function create(Request $request)
    {
        $keberangkatanList = Keberangkatan::orderBy('tanggal_berangkat','desc')->get();

        $jamaahList = collect();

        if ($request->keberangkatan_id) {
            // Ambil jamaah yang belum masuk manifest keberangkatan ini
            $jamaahList = Jamaah::whereNotIn('id', function($q) use ($request){
                $q->select('jamaah_id')->from('manifest')
                  ->where('keberangkatan_id', $request->keberangkatan_id);
            })->orderBy('nama_lengkap')->get();
        }

        return view('operator.manifest.create', compact('keberangkatanList','jamaahList'));
    }


    /**
     * AUTO NOMOR KAMAR FORMAT PREMIUM
     * Quad   → Q101, Q102...
     * Triple → T201, T202...
     * Double → D301, D302...
     */
    private function generateNomorKamar($tipe, $keberangkatan_id)
    {
        $prefix = match($tipe) {
            'Quad'   => 'Q',
            'Triple' => 'T',
            'Double' => 'D',
            default  => 'X'
        };

        $last = Manifest::where('keberangkatan_id',$keberangkatan_id)
                 ->where('tipe_kamar',$tipe)
                 ->orderBy('nomor_kamar','desc')
                 ->first();

        if (!$last) {
            return $prefix . '101';
        }

        // Ambil angka belakang contoh: Q105 → 105
        $num = intval(substr($last->nomor_kamar, 1)) + 1;

        return $prefix . $num;
    }


    /**
     * STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'keberangkatan_id' => 'required',
            'jamaah_id'        => 'required',
            'tipe_kamar'       => 'required|in:Quad,Triple,Double',
        ]);

        // Prevent duplicate
        $exists = Manifest::where('keberangkatan_id', $request->keberangkatan_id)
                    ->where('jamaah_id', $request->jamaah_id)
                    ->first();
        if ($exists) {
            return back()->with('error','Jamaah ini sudah masuk manifest!');
        }

        // Auto nomor kamar
        $nomor_kamar = $this->generateNomorKamar(
            $request->tipe_kamar, 
            $request->keberangkatan_id
        );

        Manifest::create([
            'keberangkatan_id' => $request->keberangkatan_id,
            'jamaah_id'        => $request->jamaah_id,
            'tipe_kamar'       => $request->tipe_kamar,
            'nomor_kamar'      => $nomor_kamar
        ]);

        return redirect()
            ->route('operator.manifest.index', ['keberangkatan_id'=>$request->keberangkatan_id])
            ->with('success','Manifest berhasil ditambahkan.');
    }


    /**
     * EDIT
     */
    public function edit($id)
    {
        $manifest = Manifest::findOrFail($id);
        $keberangkatanList = Keberangkatan::orderBy('tanggal_berangkat','desc')->get();
        $jamaahList = Jamaah::orderBy('nama_lengkap')->get();

        return view('operator.manifest.edit', compact('manifest','keberangkatanList','jamaahList'));
    }


    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $manifest = Manifest::findOrFail($id);

        $request->validate([
            'tipe_kamar' => 'required|in:Quad,Triple,Double',
            'nomor_kamar' => 'required'
        ]);

        $manifest->update([
            'tipe_kamar'  => $request->tipe_kamar,
            'nomor_kamar' => $request->nomor_kamar
        ]);

        return redirect()
            ->route('operator.manifest.index',['keberangkatan_id'=>$manifest->keberangkatan_id])
            ->with('success','Manifest berhasil diperbarui.');
    }


    /**
     * DELETE
     */
    public function destroy($id)
    {
        $m = Manifest::findOrFail($id);
        $kid = $m->keberangkatan_id;
        $m->delete();

        return redirect()->route('operator.manifest.index',['keberangkatan_id'=>$kid])
            ->with('success','Data manifest berhasil dihapus.');
    }


    /**
     * PRINT MANIFEST (PREMIUM PDF)
     * — Tanpa QR sesuai request mas
     */
   public function print(Request $request)
{
    // Validasi keberangkatan
    $keberangkatan = Keberangkatan::with('paket')->findOrFail($request->keberangkatan_id);

    // Ambil data manifest dengan relasi jamaah
    $manifests = Manifest::with('jamaah')
        ->where('keberangkatan_id', $keberangkatan->id)
        ->orderBy('tipe_kamar')
        ->orderBy('nomor_kamar')
        ->get();

    // Hitung durasi umrah (hari)
    $durasi = \Carbon\Carbon::parse($keberangkatan->tanggal_berangkat)
        ->diffInDays(\Carbon\Carbon::parse($keberangkatan->tanggal_pulang)) + 1;

    // Generate PDF
    $pdf = \PDF::loadView(
                'operator.manifest.print',
                compact('manifests', 'keberangkatan', 'durasi')
            )
            ->setPaper('A4', 'portrait');

    return $pdf->stream('Manifest-' . $keberangkatan->kode_keberangkatan . '.pdf');
}


}
