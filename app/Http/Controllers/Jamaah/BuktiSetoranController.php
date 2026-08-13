<?php

namespace App\Http\Controllers\Jamaah;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\BuktiSetoran;

class BuktiSetoranController extends Controller
{
    /**
     * PREVIEW KWITANSI SETORAN (PDF)
     * JAMAAH ONLY – READ ONLY
     */
    public function show(int $bukti)
{
    $bukti = BuktiSetoran::findOrFail($bukti);

    $jamaahUser = auth('jamaah')->user();

    abort_unless(
        $jamaahUser && $bukti->jamaah_id === $jamaahUser->jamaah_id,
        403,
        'Akses ditolak.'
    );

    $path = 'bukti-setoran/bukti-' . $bukti->id . '.pdf';
    $file = storage_path('app/' . $path);

    abort_unless(is_file($file), 404, 'Kwitansi belum tersedia.');

    return response()->file($file, [
        'Content-Type'        => 'application/pdf',
        'Content-Disposition' => 'inline; filename="Kwitansi-'.$bukti->nomor_bukti.'.pdf"',
    ]);
}

}
