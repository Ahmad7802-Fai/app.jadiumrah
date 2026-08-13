<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Keberangkatan;
use Illuminate\Http\JsonResponse;

class AjaxKeberangkatanController extends Controller
{
    public function paket(int $id): JsonResponse
    {
        $keberangkatan = Keberangkatan::with('paketMaster')
            ->find($id);

        if (!$keberangkatan || !$keberangkatan->paketMaster) {
            return response()->json([
                'paket' => null
            ]);
        }

        $paket = $keberangkatan->paketMaster;

        return response()->json([
            'paket' => [
                'id'            => $paket->id,
                'nama_paket'    => $paket->nama_paket,
                'harga_quad'    => (int) $paket->harga_quad,
                'harga_triple'  => (int) $paket->harga_triple,
                'harga_double'  => (int) $paket->harga_double,
            ]
        ]);
    }
}
