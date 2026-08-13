<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\PaketMaster;
use Illuminate\Http\Request;

class PaketMasterController extends Controller
{
    public function index(Request $r)
    {
        $q  = $r->q;
        $status = $r->status;

        $data = PaketMaster::when($q, fn($s) =>
                        $s->where('nama_paket', 'like', "%$q%")
                          ->orWhere('pesawat', 'like', "%$q%")
                        )
                        ->when($status, fn($s) =>
                            $s->where('is_active', $status == 'Aktif' ? '1' : '0')
                        )
                        ->orderBy('created_at', 'DESC')
                        ->paginate(10);

        return view('operator.paket-master.index', compact('data', 'q', 'status'));
    }

    public function create()
    {
        return view('operator.paket-master.create');
    }

    public function store(Request $r)
    {
        $r->validate([
            'nama_paket' => 'required|max:255',
            'pesawat'    => 'nullable|max:150',
            'hotel_mekkah' => 'nullable|max:255',
            'hotel_madinah' => 'nullable|max:255',
            'harga_quad'   => 'required|numeric',
            'harga_triple' => 'required|numeric',
            'harga_double' => 'required|numeric',
            'diskon_default' => 'nullable|numeric',
            'is_active' => 'required|in:0,1'
        ]);

        PaketMaster::create($r->all());

        return redirect()->route('operator.master-paket.index')
                         ->with('success', 'Master paket berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $item = PaketMaster::findOrFail($id);
        return view('operator.paket-master.edit', compact('item'));
    }

    public function update(Request $r, $id)
    {
        $item = PaketMaster::findOrFail($id);

        $r->validate([
            'nama_paket' => 'required|max:255',
            'pesawat'    => 'nullable|max:150',
            'hotel_mekkah' => 'nullable|max:255',
            'hotel_madinah' => 'nullable|max:255',
            'harga_quad'   => 'required|numeric',
            'harga_triple' => 'required|numeric',
            'harga_double' => 'required|numeric',
            'diskon_default' => 'nullable|numeric',
            'is_active' => 'required|in:0,1'
        ]);

        $item->update($r->all());

        return redirect()->route('operator.master-paket.index')
                         ->with('success', 'Master paket berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $item = PaketMaster::findOrFail($id);
        $item->delete();

        return redirect()->route('operator.master-paket.index')
                         ->with('success', 'Master paket berhasil dihapus!');
    }
}
