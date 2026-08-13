<?php
namespace App\Http\Controllers\Layanan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LayananTransaksi;
use App\Models\LayananTransaksiItem;
use App\Models\LayananItem;

class TransaksiController extends Controller
{
    // Create transaksi + multi item

    public function index()
    {
        $transaksi = LayananTransaksi::with('client')->orderBy('id','desc')->get();
        return view('layanan.transaksi.index', compact('transaksi'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_client' => 'required|integer|exists:clients,id',
            'items' => 'required|array|min:1',
            'items.*.id_layanan_item' => 'required|integer|exists:layanan_item,id',
            'items.*.qty' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        return DB::transaction(function() use ($data) {

            $firstItem = $data['items'][0];

            $transaksi = LayananTransaksi::create([
                'id_client' => $data['id_client'],
                'id_layanan_item' => $firstItem['id_layanan_item'], // fallback
                'qty' => array_sum(array_column($data['items'], 'qty')),
                'harga' => 0,
                'subtotal' => 0,
                'notes' => $data['notes'] ?? null,
                'status' => 'pending',
            ]);

            $total = 0;

            foreach ($data['items'] as $it) {
                $item = LayananItem::findOrFail($it['id_layanan_item']);
                $harga = $item->harga;
                $qty = $it['qty'];
                $subtotal = $harga * $qty;

                LayananTransaksiItem::create([
                    'id_transaksi' => $transaksi->id,
                    'id_layanan_item' => $item->id,
                    'qty' => $qty,
                    'harga' => $harga,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            $transaksi->update([
                'subtotal' => $total
            ]);

            return response()->json([
                'success' => true,
                'transaksi_id' => $transaksi->id,
                'total' => $total
            ]);
        });
    }

    public function show($id)
    {
        $data = LayananTransaksi::with(['client','items.layananItem'])->findOrFail($id);
        return response()->json($data);
    }
}
