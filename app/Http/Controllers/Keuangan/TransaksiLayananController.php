<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\LayananTransaksi;
use App\Models\LayananTransaksiItem;
use App\Models\LayananItem;
use App\Models\Client;

class TransaksiLayananController extends Controller
{
    /* ============================================================
       INDEX — LIST TRANSAKSI
    ============================================================ */
    public function index()
    {
        $transaksi = LayananTransaksi::with(['client'])
            ->withCount('items')
            ->orderByDesc('id')
            ->paginate(25);

        return view('keuangan.transaksi-layanan.index', compact('transaksi'));
    }



    /* ============================================================
       SHOW — DETAIL TRANSAKSI
    ============================================================ */
    public function show($id)
    {
        $trx = LayananTransaksi::with([
                'client',
                'items.item'  // item detail
            ])
            ->findOrFail($id);

        return view('keuangan.transaksi-layanan.show', compact('trx'));
    }



    /* ============================================================
       CREATE FORM
    ============================================================ */
    public function create()
    {
        $clients = Client::orderBy('nama')->get();
        $items   = LayananItem::orderBy('nama_item')->get();

        return view('keuangan.transaksi-layanan.create', compact('clients', 'items'));
    }



    /* ============================================================
       STORE — SIMPAN TRANSAKSI BARU
       SUPPORT: HOTEL + DAYS
    ============================================================ */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_client'               => 'required|exists:clients,id',
            'notes'                   => 'nullable|string',
            'items'                   => 'required|array|min:1',

            'items.*.id_layanan_item' => 'required|exists:layanan_item,id',
            'items.*.qty'             => 'required|numeric|min:1',
            'items.*.days'            => 'nullable|numeric|min:1',
        ]);

        DB::beginTransaction();
        try {
            $total = 0;

            foreach ($data['items'] as $it) {
                $layan = \App\Models\LayananItem::find($it['id_layanan_item']);

                $harga = (float) $layan->harga;
                $qty   = (int) $it['qty'];
                $days  = $layan->tipe === 'hotel'
                            ? max(1, (int)($it['days'] ?? 1))
                            : 1;

                $total += $harga * $qty * $days;
            }

            $trx = LayananTransaksi::create([
                'id_client'  => $data['id_client'],
                'subtotal'   => $total,
                'currency'   => 'IDR',
                'notes'      => $data['notes'] ?? null,
                'status'     => 'pending',
                'created_by' => auth()->id(),
            ]);

            foreach ($data['items'] as $it) {
                $layan = \App\Models\LayananItem::find($it['id_layanan_item']);
                $harga = (float) $layan->harga;
                $qty   = (int) $it['qty'];
                $days  = $layan->tipe === 'hotel' ? (int)($it['days'] ?? 1) : 1;

                LayananTransaksiItem::create([
                    'id_transaksi'    => $trx->id,
                    'id_layanan_item' => $layan->id,
                    'qty'             => $qty,
                    'days'            => $days,
                    'harga'           => $harga,
                    'subtotal'        => $harga * $qty * $days,
                ]);
            }

            DB::commit();
            return redirect()->route('keuangan.transaksi-layanan.index')
                            ->with('success','Transaksi layanan berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error','Gagal menyimpan: '.$e->getMessage());
        }
    }


    /* ============================================================
       EDIT FORM
    ============================================================ */
    public function edit($id)
    {
        $trx     = LayananTransaksi::with(['items.item'])->findOrFail($id);
        $clients = Client::orderBy('nama')->get();
        $items   = LayananItem::orderBy('nama_item')->get();

        return view('keuangan.transaksi-layanan.edit', compact('trx', 'clients', 'items'));
    }



    /* ============================================================
       UPDATE — MASTER + ITEMS
       MENYESUAIKAN INVOICE JIKA ADA
    ============================================================ */
   public function update(Request $request, $id)
{
    $trx = LayananTransaksi::with('items')->findOrFail($id);

    $data = $request->validate([
        'id_client' => 'required|exists:clients,id',
        'notes'     => 'nullable|string',
        'items'     => 'required|array|min:1',
        'items.*.id_layanan_item' => 'required|exists:layanan_item,id',
        'items.*.qty'  => 'required|integer|min:1',
        'items.*.days' => 'nullable|integer|min:1',
    ]);

    DB::beginTransaction();
    try {
        // 1) Hitung total baru
        $total = 0;
        foreach ($data['items'] as $it) {
            $layan = LayananItem::find($it['id_layanan_item']);
            if (! $layan) {
                throw new \Exception("Item layanan tidak ditemukan (id: {$it['id_layanan_item']})");
            }

            $harga = (float) $layan->harga;
            $qty   = (int) $it['qty'];
            // days default 1 (jika null)
            $days  = isset($it['days']) && $it['days'] > 0 ? (int)$it['days'] : 1;

            $subtotal = $harga * $qty * $days;
            $total += $subtotal;
        }

        // 2) Update transaksi master
        $trx->update([
            'id_client' => $data['id_client'],
            'subtotal'  => $total,
            'notes'     => $data['notes'] ?? null,
            'status'    => $trx->status, // jaga status tetap
        ]);

        // 3) Hapus item lama (simple & aman) lalu recreate
        LayananTransaksiItem::where('id_transaksi', $trx->id)->delete();

        foreach ($data['items'] as $it) {
            $layan = LayananItem::find($it['id_layanan_item']);
            $harga = (float) $layan->harga;
            $qty   = (int) $it['qty'];
            $days  = isset($it['days']) && $it['days'] > 0 ? (int)$it['days'] : 1;
            $subtotal = $harga * $qty * $days;

            LayananTransaksiItem::create([
                'id_transaksi'    => $trx->id,
                'id_layanan_item' => $it['id_layanan_item'],
                'qty'             => $qty,
                'days'            => $days,
                'harga'           => $harga,
                'subtotal'        => $subtotal,
            ]);
        }

        // 4) Jika ada invoice terhubung, sinkronkan jumlah / status
        if ($trx->invoice) {
            $invoice = $trx->invoice;
            // pastikan kolom invoice: amount & paid_amount sesuai skema
            $invoice->amount = $trx->subtotal;
            // tetapkan status berdasarkan paid_amount
            $invoice->status = ($invoice->paid_amount >= $trx->subtotal) ? 'paid' : ($invoice->paid_amount > 0 ? 'partial' : 'unpaid');
            $invoice->save();
        }

        DB::commit();

        return redirect()
            ->route('keuangan.transaksi-layanan.index')
            ->with('success', 'Transaksi layanan berhasil diperbarui.');
    } catch (\Throwable $e) {
        DB::rollBack();
        // bantu debugging sementara: log
        \Log::error('Update transaksi gagal: '.$e->getMessage(), ['trx_id'=>$id, 'error'=>$e]);
        return back()->withInput()->with('error', 'Gagal memperbarui transaksi: '.$e->getMessage());
    }
}



    /* ============================================================
       DESTROY — HAPUS TRANSAKSI
    ============================================================ */
    public function destroy($id)
    {
        $trx = LayananTransaksi::findOrFail($id);

        DB::beginTransaction();
        try {
            $trx->delete(); // CASCADE delete items

            DB::commit();
            return redirect()
                ->route('keuangan.transaksi-layanan.index')
                ->with('success', 'Transaksi berhasil dihapus.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus transaksi: '.$e->getMessage());
        }
    }
}


// namespace App\Http\Controllers\Keuangan;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Models\LayananTransaksi;
// use App\Models\LayananTransaksiItem;
// use App\Models\LayananItem;
// use App\Models\Client;

// class TransaksiLayananController extends Controller
// {
//     /* ====================================================
//        INDEX
//     ==================================================== */
//     public function index()
//     {
//         $transaksi = LayananTransaksi::with('client')
//                         ->withCount('items')
//                         ->orderBy('id', 'desc')
//                         ->get();

//         return view('keuangan.transaksi-layanan.index', compact('transaksi'));
//     }


//     /* ====================================================
//        SHOW
//     ==================================================== */
//     public function show($id)
//     {
//         $trx = LayananTransaksi::with([
//                     'client',
//                     'items.item'   // relasi benar
//                 ])
//                 ->findOrFail($id);

//         return view('keuangan.transaksi-layanan.show', compact('trx'));
//     }


//     /* ====================================================
//        CREATE
//     ==================================================== */
//     public function create()
//     {
//         $clients = Client::orderBy('nama')->get();
//         $items   = LayananItem::orderBy('nama_item')->get();

//         return view('keuangan.transaksi-layanan.create', compact('clients', 'items'));
//     }


//     /* ====================================================
//        STORE
//     ==================================================== */
//     public function store(Request $request)
//     {
//         $data = $request->validate([
//             'id_client' => 'required|exists:clients,id',
//             'notes'     => 'nullable|string',
//             'items'     => 'required|array|min:1',
//             'items.*.id_layanan_item' => 'required|exists:layanan_item,id',
//             'items.*.qty' => 'required|numeric|min:1',
//         ]);

//         // Hitung total
//         $total = 0;
//         foreach ($data['items'] as $item) {
//             $harga = LayananItem::find($item['id_layanan_item'])->harga;
//             $total += $harga * $item['qty'];
//         }

//         // Simpan transaksi
//         $trx = LayananTransaksi::create([
//             'id_client' => $data['id_client'],
//             'subtotal'  => $total,
//             'currency'  => 'IDR',
//             'notes'     => $data['notes'] ?? null,
//             'status'    => 'pending',
//         ]);

//         // Simpan item transaksi
//         foreach ($data['items'] as $item) {
//             $layan = LayananItem::find($item['id_layanan_item']);
//             $harga = $layan->harga;
//             $subtotal = $harga * $item['qty'];

//             LayananTransaksiItem::create([
//                 'id_transaksi'     => $trx->id,
//                 'id_layanan_item'  => $item['id_layanan_item'],
//                 'qty'              => $item['qty'],
//                 'harga'            => $harga,
//                 'subtotal'         => $subtotal,
//             ]);
//         }

//         return redirect()
//             ->route('keuangan.transaksi-layanan.index')
//             ->with('success', 'Transaksi layanan berhasil disimpan.');
//     }


//     /* ====================================================
//        EDIT
//     ==================================================== */
//     public function edit($id)
//     {
//         $trx     = LayananTransaksi::with(['items.item'])->findOrFail($id);
//         $clients = Client::orderBy('nama')->get();
//         $items   = LayananItem::orderBy('nama_item')->get();

//         return view('keuangan.transaksi-layanan.edit', compact('trx', 'clients', 'items'));
//     }


//     /* ====================================================
//        UPDATE (DENGAN AUTO-SYNC INVOICE)
//     ==================================================== */
//     public function update(Request $request, $id)
//     {
//         $trx = LayananTransaksi::findOrFail($id);

//         $data = $request->validate([
//             'id_client' => 'required|exists:clients,id',
//             'notes'     => 'nullable|string',
//             'items'     => 'required|array|min:1',
//             'items.*.id_layanan_item' => 'required|exists:layanan_item,id',
//             'items.*.qty' => 'required|numeric|min:1',
//         ]);

//         // Hitung total baru
//         $total = 0;
//         foreach ($data['items'] as $item) {
//             $harga = LayananItem::find($item['id_layanan_item'])->harga;
//             $total += $harga * $item['qty'];
//         }

//         // Update transaksi
//         $trx->update([
//             'id_client' => $data['id_client'],
//             'subtotal'  => $total,
//             'notes'     => $data['notes'] ?? null,
//         ]);

//         // Hapus item lama
//         LayananTransaksiItem::where('id_transaksi', $trx->id)->delete();

//         // Tambahkan item baru
//         foreach ($data['items'] as $item) {
//             $layan  = LayananItem::find($item['id_layanan_item']);
//             $harga  = $layan->harga;
//             $sub    = $harga * $item['qty'];

//             LayananTransaksiItem::create([
//                 'id_transaksi'     => $trx->id,
//                 'id_layanan_item'  => $item['id_layanan_item'],
//                 'qty'              => $item['qty'],
//                 'harga'            => $harga,
//                 'subtotal'         => $sub,
//             ]);
//         }


//         /* ====================================================
//            AUTO UPDATE INVOICE — PREMIUM FIX
//         ==================================================== */
//         if ($trx->invoice) {

//             $trx->invoice->update([
//                 'amount' => $trx->subtotal,
//                 'status' => $trx->invoice->paid_amount >= $trx->subtotal
//                                 ? 'paid'
//                                 : 'unpaid',
//             ]);

//         }


//         return redirect()
//             ->route('keuangan.transaksi-layanan.index')
//             ->with('success', 'Transaksi layanan berhasil diperbarui (invoice otomatis diperbarui).');
//     }


//     /* ====================================================
//        DESTROY
//     ==================================================== */
//     public function destroy($id)
//     {
//         $trx = LayananTransaksi::findOrFail($id);
//         $trx->delete();

//         return redirect()
//             ->route('keuangan.transaksi-layanan.index')
//             ->with('success', 'Transaksi layanan berhasil dihapus.');
//     }
// }
