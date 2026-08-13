<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ItemController extends Controller
{
    /** LIST **/
    public function index()
    {
        $items = Item::orderBy('nama_barang')->paginate(20);

        return view('inventory.items.index', compact('items'));
    }

    /** CREATE **/
    public function create()
    {
        return view('inventory.items.create');
    }

    /** STORE **/
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required',
            'satuan'      => 'nullable',
            'kategori'    => 'nullable',
            'harga_beli'  => 'numeric|min:0',
            'harga_jual'  => 'numeric|min:0',
        ]);

        // AUTO KODE
        $last = Item::orderBy('id', 'desc')->first();
        $next = $last ? $last->id + 1 : 1;
        $kode = 'BRG-' . str_pad($next, 4, '0', STR_PAD_LEFT);

        Item::create([
            'kode_barang' => $kode,
            'nama_barang' => $request->nama_barang,
            'satuan'      => $request->satuan ?? 'pcs',
            'kategori'    => $request->kategori,
            'harga_beli'  => $request->harga_beli ?? 0,
            'harga_jual'  => $request->harga_jual ?? 0,
        ]);

        return redirect()->route('inventory.items.index')
            ->with('success','Barang berhasil ditambahkan.');
    }

    /** EDIT **/
    public function edit($id)
    {
        $item = Item::findOrFail($id);
        return view('inventory.items.edit', compact('item'));
    }

    /** UPDATE **/
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_barang' => 'required',
            'harga_beli'  => 'numeric|min:0',
            'harga_jual'  => 'numeric|min:0',
        ]);

        $item = Item::findOrFail($id);

        $item->update([
            'nama_barang' => $request->nama_barang,
            'satuan'      => $request->satuan,
            'kategori'    => $request->kategori,
            'harga_beli'  => $request->harga_beli,
            'harga_jual'  => $request->harga_jual,
        ]);

        return redirect()->route('inventory.items.index')
            ->with('success','Barang berhasil diupdate.');
    }

    /** DELETE **/
    public function destroy($id)
    {
        Item::findOrFail($id)->delete();

        return redirect()->route('inventory.items.index')
            ->with('success','Barang berhasil dihapus.');
    }

    /** EXPORT PDF F4 **/
    public function exportPdf()
    {
        $items = Item::orderBy('nama_barang')->get();

        $pdf = Pdf::loadView('inventory.items.pdf-f4', compact('items'))
            ->setPaper('F4','portrait');

        return $pdf->stream('Master-Barang.pdf');
    }

    /** EXPORT EXCEL **/
    public function exportExcel()
    {
        return Excel::download(new \App\Exports\ItemsExport, 'Master-Barang.xlsx');
    }
}
