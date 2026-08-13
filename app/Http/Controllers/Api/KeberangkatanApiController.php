<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Keberangkatan;

class KeberangkatanApiController extends Controller
{
    public function paket(int $id)
    {
        $keberangkatan = Keberangkatan::with('paketMaster')
            ->where('status', 'Aktif')
            ->findOrFail($id);

        if (! $keberangkatan->paketMaster) {
            return response()->json([
                'paket' => null
            ]);
        }

        return response()->json([
            'paket' => [
                'id'            => $keberangkatan->paketMaster->id,
                'nama_paket'    => $keberangkatan->paketMaster->nama_paket,
                'harga_quad'    => $keberangkatan->paketMaster->harga_quad,
                'harga_triple'  => $keberangkatan->paketMaster->harga_triple,
                'harga_double'  => $keberangkatan->paketMaster->harga_double,
            ]
        ]);
    }
}
