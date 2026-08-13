<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\LayananMaster;
use App\Models\LayananItem;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    public function index()
    {
        $layanan = LayananMaster::withCount('items')->orderBy('id','desc')->get();
        return view('keuangan.layanan.index', compact('layanan'));
    }

    public function create()
    {
        return view('keuangan.layanan.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_layanan' => 'required|string|max:50',
            'nama_layanan' => 'required|string|max:255',
            'kategori' => 'required|in:ticket,visa,land,other',
            'deskripsi' => 'nullable|string',
        ]);

        LayananMaster::create($data);

        return redirect()->route('keuangan.layanan.index')
            ->with('success','Layanan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $layanan = LayananMaster::with('items')->findOrFail($id);
        return view('keuangan.layanan.show', compact('layanan'));
    }
}
