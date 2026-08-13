<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Stock;
use App\Models\StockMutation;

class LogMutasiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $mutasi = StockMutation::with('barang')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('inventory.mutasi.index', compact('mutasi'));
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $items = Item::orderBy('nama_barang')->get();
        return view('inventory.mutasi.create', compact('items'));
    }


    /*
    |--------------------------------------------------------------------------
    | STORE (CREATE MUTATION)
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'item_id'    => 'required',
            'tipe'       => 'required|in:IN,OUT',
            'jumlah'     => 'required|numeric|min:1',
            'keterangan' => 'nullable',
        ]);

        $stock = Stock::where('item_id', $request->item_id)->firstOrFail();

        // SIMPAN LOG MUTASI
        StockMutation::create([
            'item_id'    => $request->item_id,
            'tanggal'    => now(),
            'tipe'       => $request->tipe,
            'jumlah'     => $request->jumlah,
            'keterangan' => $request->keterangan,
            'sumber'     => 'manual',
        ]);

        // UPDATE STOK
        if ($request->tipe == 'IN') {
            $stock->stok += $request->jumlah;
        } else {
            if ($stock->stok < $request->jumlah) {
                return back()->with('error', 'Stok tidak mencukupi.');
            }
            $stock->stok -= $request->jumlah;
        }

        $stock->save();

        return redirect()
            ->route('inventory.mutasi.index')
            ->with('success', 'Mutasi stok berhasil disimpan.');
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $mutasi = StockMutation::findOrFail($id);
        $items  = Item::orderBy('nama_barang')->get();

        return view('inventory.mutasi.edit', compact('mutasi', 'items'));
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE MUTATION
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $mutasi = StockMutation::findOrFail($id);
        $stock  = Stock::where('item_id', $mutasi->item_id)->firstOrFail();

        // ROLLBACK STOK LAMA
        if ($mutasi->tipe == 'IN') {
            $stock->stok -= $mutasi->jumlah;
        } else {
            $stock->stok += $mutasi->jumlah;
        }

        $request->validate([
            'item_id'    => 'required',
            'tipe'       => 'required|in:IN,OUT',
            'jumlah'     => 'required|numeric|min:1',
            'keterangan' => 'nullable',
        ]);

        // UPDATE DATA MUTASI
        $mutasi->update([
            'item_id'    => $request->item_id,
            'tipe'       => $request->tipe,
            'jumlah'     => $request->jumlah,
            'keterangan' => $request->keterangan,
        ]);

        // UPDATE STOK BARU
        if ($request->tipe == 'IN') {
            $stock->stok += $request->jumlah;
        } else {
            if ($stock->stok < $request->jumlah) {
                return back()->with('error', 'Stok tidak mencukupi.');
            }
            $stock->stok -= $request->jumlah;
        }

        $stock->save();

        return redirect()
            ->route('inventory.mutasi.index')
            ->with('success', 'Mutasi berhasil diperbarui.');
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE MUTATION
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $mutasi = StockMutation::findOrFail($id);
        $stock  = Stock::where('item_id', $mutasi->item_id)->firstOrFail();

        // KEMBALIKAN STOK
        if ($mutasi->tipe == 'IN') {
            $stock->stok -= $mutasi->jumlah;
        } else {
            $stock->stok += $mutasi->jumlah;
        }

        $stock->save();
        $mutasi->delete();

        return back()->with('success', 'Mutasi dihapus & stok dikembalikan.');
    }

}
