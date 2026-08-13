<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Item;

class StokBarangController extends Controller
{
    public function index()
    {
        $stok = Stock::with('barang')->orderBy('item_id')->get();
        return view('inventory.stok.index', compact('stok'));
    }

    public function create()
    {
        $items = Item::orderBy('nama_barang')->get();
        return view('inventory.stok.create', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required',
            'stok'    => 'required|numeric|min:1',
        ]);

        // Cari stok barang
        $stock = Stock::where('item_id', $request->item_id)->first();

        // Jika belum ada → buat baru
        if (!$stock) {
            $stock = new Stock();
            $stock->item_id = $request->item_id;
            $stock->stok = 0; // default sebelum ditambahkan
        }

        // Tambah stok baru
        $stock->stok += $request->stok;
        $stock->save();

        return redirect()
            ->route('inventory.stok.index')
            ->with('success', 'Stok berhasil ditambahkan.');
    }


    public function edit($id)
    {
        $stok = Stock::with('item')->findOrFail($id);

        return view('inventory.stok.edit', compact('stok'));
    }


    public function update(Request $request, $id)
    {
        $stock = Stock::findOrFail($id);

        $stock->update([
            'stok' => $request->stok,
        ]);

        return redirect()->route('inventory.stok.index')->with('success', 'Stok diperbarui.');
    }

    public function destroy($id)
    {
        Stock::findOrFail($id)->delete();
        return back()->with('success', 'Stok dihapus.');
    }
}
