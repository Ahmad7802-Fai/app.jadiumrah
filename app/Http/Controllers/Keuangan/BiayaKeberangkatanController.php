<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Keberangkatan;

class BiayaKeberangkatanController extends Controller
{
    /**
     * Halaman daftar keberangkatan khusus menu KEUANGAN.
     * Operator memiliki controller keberangkatan sendiri.
     */
    public function index()
    {
        $data = Keberangkatan::with('paket')
            ->withCount('jamaah as total_jamaah')
            ->withSum(
                ['tripExpenses as total_biaya'],
                'jumlah'
            )
            ->orderBy('tanggal_berangkat', 'desc')
            ->paginate(20);

        return view('keuangan.biaya-keberangkatan.index', compact('data'));
    }

}
