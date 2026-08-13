<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\LayananItem;
use App\Models\LayananMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LayananItemController extends Controller
{
    /**
     * FORM CREATE ITEM
     */
    public function create($id_master)
    {
        $master = LayananMaster::findOrFail($id_master);

        return view('keuangan.layanan.items.create', compact('master'));
    }


    /**
     * STORE ITEM BARU (SUPPORT HOTEL + DAYS)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_layanan_master'   => 'required|exists:layanan_master,id',
            'nama_item'           => 'required|string|max:255',
            'harga'               => 'required|numeric|min:0',

            // F7 NEW FIELD
            'tipe'                => 'required|in:default,hotel',
            'durasi_hari_default' => 'nullable|numeric|min:1',

            'satuan'              => 'nullable|string|max:50',
            'vendor'              => 'nullable|string|max:255',
            'tanggal_mulai'       => 'nullable|date',
            'tanggal_selesai'     => 'nullable|date',
            'currency'            => 'nullable|string|max:10',
            'status'              => 'nullable|in:0,1',
        ]);

        // === DEFAULT HANDLING ===
        $data['status']   = $data['status'] ?? 1;
        $data['currency'] = $data['currency'] ?? 'IDR';

        // === HANDLE HOTEL ===
        if ($data['tipe'] === 'hotel') {
            // Jika hotel → duration wajib, atau default minimal 1
            $data['durasi_hari_default'] = $data['durasi_hari_default'] ?? 1;
        } else {
            // Non-hotel → durasi di-nolkan
            $data['durasi_hari_default'] = null;
        }

        LayananItem::create($data);

        return redirect()
            ->route('keuangan.layanan.show', $data['id_layanan_master'])
            ->with('success', 'Item layanan berhasil ditambahkan.');
    }


    /**
     * FORM EDIT ITEM
     */
    public function edit($id)
    {
        $item   = LayananItem::findOrFail($id);
        $master = $item->master;

        return view('keuangan.layanan.items.edit', compact('item', 'master'));
    }


    /**
     * UPDATE ITEM (SUPPORT HOTEL + DAYS)
     */
    public function update(Request $request, $id)
    {
        $item = LayananItem::findOrFail($id);

        $data = $request->validate([
            'nama_item'           => 'required|string|max:255',
            'harga'               => 'required|numeric|min:0',

            // F7 NEW FIELD
            'tipe'                => 'required|in:default,hotel',
            'durasi_hari_default' => 'nullable|numeric|min:1',

            'satuan'              => 'nullable|string|max:50',
            'vendor'              => 'nullable|string|max:255',
            'tanggal_mulai'       => 'nullable|date',
            'tanggal_selesai'     => 'nullable|date',
            'currency'            => 'nullable|string|max:10',
            'status'              => 'nullable|in:0,1',
        ]);

        // === HANDLE TYPE ===
        if ($data['tipe'] === 'hotel') {
            $data['durasi_hari_default'] = $data['durasi_hari_default'] ?? 1;
        } else {
            $data['durasi_hari_default'] = null;
        }

        $item->update($data);

        return redirect()
            ->route('keuangan.layanan.show', $item->id_layanan_master)
            ->with('success', 'Item layanan berhasil diperbarui.');
    }


    /**
     * DELETE ITEM (SAFE MODE)
     */
    public function destroy($id)
    {
        $item      = LayananItem::findOrFail($id);
        $id_master = $item->id_layanan_master;

        // Cek apakah item dipakai transaksi
        $dipakai = DB::table('layanan_transaksi_items')
                        ->where('id_layanan_item', $id)
                        ->count();

        if ($dipakai > 0) {
            return redirect()
                ->route('keuangan.layanan.show', $id_master)
                ->with('error', 'Item ini tidak dapat dihapus karena sudah dipakai transaksi.');
        }

        $item->delete();

        return redirect()
            ->route('keuangan.layanan.show', $id_master)
            ->with('success', 'Item layanan berhasil dihapus.');
    }


    /**
     * TOGGLE STATUS
     */
    public function toggleStatus($id)
    {
        $item = LayananItem::findOrFail($id);

        $item->status = $item->status ? 0 : 1;
        $item->save();

        $label = $item->status ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->route('keuangan.layanan.show', $item->id_layanan_master)
            ->with('success', "Item layanan berhasil {$label}.");
    }
}

// namespace App\Http\Controllers\Keuangan;

// use App\Http\Controllers\Controller;
// use App\Models\LayananItem;
// use App\Models\LayananMaster;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;

// class LayananItemController extends Controller
// {
//     /**
//      * Form Create Item
//      */
//     public function create($id_master)
//     {
//         $master = LayananMaster::findOrFail($id_master);
//         return view('keuangan.layanan.items.create', compact('master'));
//     }


//     /**
//      * Store Item Baru
//      */
//     public function store(Request $request)
//     {
//         $data = $request->validate([
//             'id_layanan_master' => 'required|exists:layanan_master,id',
//             'nama_item'         => 'required|string|max:255',
//             'harga'             => 'required|numeric',
//             'satuan'            => 'nullable|string|max:50',
//             'vendor'            => 'nullable|string|max:255',
//             'tanggal_mulai'     => 'nullable|date',
//             'tanggal_selesai'   => 'nullable|date',
//             'currency'          => 'nullable|string|max:10',
//             'status'            => 'nullable|in:0,1',
//         ]);

//         // default
//         $data['status']   = $data['status'] ?? 1;
//         $data['currency'] = $data['currency'] ?? 'IDR';

//         $item = LayananItem::create($data);

//         return redirect()
//             ->route('keuangan.layanan.show', $item->id_layanan_master)
//             ->with('success', 'Item layanan berhasil ditambahkan.');
//     }


//     /**
//      * Form Edit Item
//      */
//     public function edit($id)
//     {
//         $item   = LayananItem::findOrFail($id);
//         $master = $item->master;

//         return view('keuangan.layanan.items.edit', compact('item', 'master'));
//     }


//     /**
//      * Update Item
//      */
//     public function update(Request $request, $id)
//     {
//         $item = LayananItem::findOrFail($id);

//         $data = $request->validate([
//             'nama_item'       => 'required|string|max:255',
//             'harga'           => 'required|numeric',
//             'satuan'          => 'nullable|string|max:50',
//             'vendor'          => 'nullable|string|max:255',
//             'tanggal_mulai'   => 'nullable|date',
//             'tanggal_selesai' => 'nullable|date',
//             'currency'        => 'nullable|string|max:10',
//         ]);

//         $item->update($data);

//         return redirect()
//             ->route('keuangan.layanan.show', $item->id_layanan_master)
//             ->with('success', 'Item layanan berhasil diperbarui.');
//     }


//     /**
//      * Delete Item (AMAN)
//      */
//     public function destroy($id)
//     {
//         $item      = LayananItem::findOrFail($id);
//         $id_master = $item->id_layanan_master;

//         // cek apakah item digunakan transaksi
//         $dipakai = DB::table('layanan_transaksi_items')
//                         ->where('id_layanan_item', $id)
//                         ->count();

//         if ($dipakai > 0) {
//             return redirect()
//                 ->route('keuangan.layanan.show', $id_master)
//                 ->with('error', 'Item ini tidak dapat dihapus karena sudah digunakan pada transaksi layanan.');
//         }

//         // aman untuk dihapus
//         $item->delete();

//         return redirect()
//             ->route('keuangan.layanan.show', $id_master)
//             ->with('success', 'Item layanan berhasil dihapus.');
//     }



//     /**
//      * Aktifkan / Nonaktifkan Item
//      */
//     public function toggleStatus($id)
//     {
//         $item = LayananItem::findOrFail($id);

//         // toggle status
//         $item->status = $item->status ? 0 : 1;
//         $item->save();

//         $label = $item->status ? 'diaktifkan' : 'dinonaktifkan';

//         return redirect()
//             ->route('keuangan.layanan.show', $item->id_layanan_master)
//             ->with('success', "Item layanan berhasil {$label}.");
//     }
// }
