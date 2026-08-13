<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Stock;
use App\Models\DistributionMaster;
use App\Models\DistributionItem;
use App\Models\StockMutation;
use DB;

class DistribusiController extends Controller
{
    /**
     * INDEX – LIST DISTRIBUSI
     */
    public function index()
    {
        $list = DistributionMaster::with('items.barang')
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('inventory.distribusi.index', compact('list'));
    }

    /**
     * CREATE PAGE
     */
    public function create()
    {
        $items = Item::orderBy('nama_barang')->get();
        return view('inventory.distribusi.create', compact('items'));
    }

    /**
     * STORE DISTRIBUSI
     */
    public function store(Request $req)
    {
        $req->validate([
            'tanggal'      => 'required|date',
            'tujuan'       => 'required',
            'item_id.*'    => 'required',
            'jumlah.*'     => 'required|numeric|min:1'
        ]);

        DB::beginTransaction();

        try {
            // === SIMPAN MASTER ===
            $master = DistributionMaster::create([
                'tanggal' => $req->tanggal,
                'tujuan'  => $req->tujuan,
                'catatan' => $req->catatan,
            ]);

            // === LOOP ITEM ===
            foreach ($req->item_id as $i => $itemId) {

                $jumlah = $req->jumlah[$i];

                DistributionItem::create([
                    'distribution_id' => $master->id,
                    'item_id'         => $itemId,
                    'jumlah'          => $jumlah,
                ]);

                // KURANGI STOK
                $stock = Stock::where('item_id', $itemId)->firstOrFail();

                if ($stock->stok < $jumlah) {
                    throw new \Exception("Stok barang tidak cukup!");
                }

                $stock->stok -= $jumlah;
                $stock->save();

                // LOG MUTASI
                StockMutation::create([
                    'item_id'    => $itemId,
                    'tanggal'    => $req->tanggal,
                    'tipe'       => 'OUT',
                    'jumlah'     => $jumlah,
                    'keterangan' => "Distribusi ke $req->tujuan",
                    'sumber'     => 'distribusi',
                    'referensi_id' => $master->id
                ]);
            }

            DB::commit();

            return redirect()
                ->route('inventory.distribusi.index')
                ->with('success', 'Distribusi berhasil disimpan.');

        } catch (\Exception $e) {

            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * EDIT PAGE
     */
    public function edit($id)
    {
        $dist  = DistributionMaster::with('items')->findOrFail($id);
        $items = Item::orderBy('nama_barang')->get();

        return view('inventory.distribusi.edit', compact('dist', 'items'));
    }

    /**
     * UPDATE DISTRIBUSI
     */
    public function update(Request $req, $id)
    {
        $req->validate([
            'tanggal'  => 'required',
            'tujuan'   => 'required'
        ]);

        DB::beginTransaction();

        try {

            $dist = DistributionMaster::findOrFail($id);

            // ROLLBACK STOK + DELETE ITEMS LAMA
            foreach ($dist->items as $old) {

                $stock = Stock::where('item_id', $old->item_id)->firstOrFail();
                $stock->stok += $old->jumlah;
                $stock->save();

                //hapus mutasi
                StockMutation::where('referensi_id', $dist->id)
                    ->where('item_id', $old->item_id)
                    ->delete();
            }

            DistributionItem::where('distribution_id', $dist->id)->delete();

            // UPDATE MASTER
            $dist->update([
                'tanggal' => $req->tanggal,
                'tujuan'  => $req->tujuan,
                'catatan' => $req->catatan
            ]);

            // INSERT ITEM BARU
            foreach ($req->item_id as $i => $itemId) {

                $jumlah = $req->jumlah[$i];

                DistributionItem::create([
                    'distribution_id' => $dist->id,
                    'item_id'         => $itemId,
                    'jumlah'          => $jumlah,
                ]);

                $stock = Stock::where('item_id', $itemId)->firstOrFail();

                if ($stock->stok < $jumlah) {
                    throw new \Exception("Stok barang tidak cukup!");
                }

                $stock->stok -= $jumlah;
                $stock->save();

                StockMutation::create([
                    'item_id'    => $itemId,
                    'tanggal'    => $req->tanggal,
                    'tipe'       => 'OUT',
                    'jumlah'     => $jumlah,
                    'keterangan' => "Distribusi ke $req->tujuan",
                    'sumber'     => 'distribusi',
                    'referensi_id' => $dist->id
                ]);
            }

            DB::commit();

            return redirect()
                ->route('inventory.distribusi.index')
                ->with('success', 'Distribusi berhasil diperbarui.');

        } catch (\Exception $e) {

            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * HAPUS DISTRIBUSI
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $dist = DistributionMaster::with('items')->findOrFail($id);

            foreach ($dist->items as $it) {
                $stock = Stock::where('item_id', $it->item_id)->first();
                $stock->stok += $it->jumlah;
                $stock->save();
            }

            StockMutation::where('referensi_id', $dist->id)
                ->where('sumber', 'distribusi')
                ->delete();

            $dist->delete();

            DB::commit();

            return back()->with('success', 'Distribusi berhasil dihapus.');

        } catch (\Exception $e) {

            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
