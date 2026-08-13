<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Keberangkatan;

class KeberangkatanPaketController extends Controller
{
    public function show(Keberangkatan $keberangkatan)
    {
        $keberangkatan->load('paketMaster');

        if (! $keberangkatan->paketMaster) {
            return response()->json([
                'paket' => null
            ]);
        }

        $paket = $keberangkatan->paketMaster;

        return response()->json([
            'paket' => [
                'id'           => $paket->id,
                'nama_paket'   => $paket->nama_paket,
                'harga_quad'   => (int) $paket->harga_quad,
                'harga_triple' => (int) $paket->harga_triple,
                'harga_double' => (int) $paket->harga_double,
            ]
        ]);
    }
}
