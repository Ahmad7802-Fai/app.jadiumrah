<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\BuktiSetoran;

class BuktiSetoranController extends Controller
{
    /**
     * GENERATE PDF BUKTI SETORAN (IMMUTABLE)
     * Role: KEUANGAN
     */
    public function regenerate(int $id)
    {
        $bukti = BuktiSetoran::with([
            'tabunganTransaksi','jamaah','tabungan','approver'
        ])->findOrFail($id);

        $path = $this->pdfPath($bukti->id);

        Storage::disk('local')->delete($path);

        $pdf = Pdf::loadView(
            'keuangan.tabungan.bukti-setoran-pdf',
            compact('bukti')
        )->setPaper('A4', 'portrait');

        Storage::disk('local')->put($path, $pdf->output());

        return back()->with('success', 'PDF bukti setoran berhasil diregenerate.');
    }


    /**
     * DOWNLOAD PDF (READ ONLY)
     */
    public function download(int $id)
    {
        $bukti = BuktiSetoran::findOrFail($id);

        $path = $this->pdfPath($bukti->id);

        abort_if(
            !Storage::disk('local')->exists($path),
            404,
            'File bukti setoran belum tersedia.'
        );

        return response()->file(
            storage_path('app/' . $path),
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Bukti-Setoran-' . $bukti->nomor_bukti . '.pdf"',
            ]
        );
    }

    /**
     * PATH PDF (IMMUTABLE)
     */
    protected function pdfPath(int $buktiId): string
    {
        return 'bukti-setoran/bukti-' . $buktiId . '.pdf';
    }

    public function preview(int $id)
    {
        $bukti = BuktiSetoran::with(...)->findOrFail($id);

        return Pdf::loadView(
            'keuangan.tabungan.bukti-setoran-pdf',
            compact('bukti')
        )->stream();
    }
}
