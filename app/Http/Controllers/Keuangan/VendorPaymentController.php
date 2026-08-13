<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VendorPayment;
use App\Models\LayananItem;

class VendorPaymentController extends Controller
{
    /**
     * LIST PEMBAYARAN VENDOR
     */
    public function index(Request $request)
    {
        $query = VendorPayment::with('layananItem');

        // filter vendor
        if ($request->vendor) {
            $query->where('vendor_name', 'LIKE', "%{$request->vendor}%");
        }

        // filter tanggal
        if ($request->from && $request->to) {
            $query->whereBetween('payment_date', [$request->from, $request->to]);
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(20);

        return view('keuangan.vendor-payments.index', compact('payments'));
    }

    /**
     * FORM TAMBAH PEMBAYARAN VENDOR
     */
   public function create()
    {
        // Ambil semua layanan item yang memiliki vendor (karena pembayaran ke vendor)
        $layananItems = \App\Models\LayananItem::whereNotNull('vendor')
                        ->orderBy('nama_item')
                        ->get();

        return view('keuangan.vendor-payments.create', compact('layananItems'));
    }


    /**
     * SIMPAN PEMBAYARAN VENDOR
     */
    public function store(Request $request)
    {
        $request->validate([
            'layanan_item_id' => 'required|exists:layanan_item,id',
            'amount'          => 'required|numeric|min:1',
            'payment_date'    => 'required|date',
        ]);

        $item = LayananItem::findOrFail($request->layanan_item_id);

        $data = $request->all();
        $data['vendor_name'] = $item->vendor;

        // upload bukti
        if ($request->hasFile('proof_file')) {
            $data['proof_file'] = $request->proof_file->store('vendor-payments', 'public');
        }

        VendorPayment::create($data);

        return redirect()->route('keuangan.vendor-payments.index')
            ->with('success', 'Pembayaran vendor berhasil ditambahkan.');
    }

    /**
     * DETAIL PEMBAYARAN
     */
    public function show($id)
    {
        $payment = VendorPayment::with('layanan_item')->findOrFail($id);

        return view('keuangan.vendor-payments.show', compact('payment'));
    }


    /**
     * EDIT PEMBAYARAN
     */
    public function edit($id)
    {
        $payment = VendorPayment::findOrFail($id);

        $layananItems = LayananItem::whereNotNull('vendor')
                        ->orderBy('nama_item')
                        ->get();

        return view('keuangan.vendor-payments.edit', compact('payment','layananItems'));
    }

    /**
     * UPDATE PEMBAYARAN
     */
    public function update(Request $request, $id)
    {
        $payment = VendorPayment::findOrFail($id);

        $request->validate([
            'amount'       => 'required|numeric|min:1',
            'payment_date' => 'required|date',
        ]);

        $data = $request->all();

        // replace bukti
        if ($request->hasFile('proof_file')) {
            $data['proof_file'] = $request->proof_file->store('vendor-payments', 'public');
        }

        $payment->update($data);

        return redirect()->route('keuangan.vendor-payments.index')
            ->with('success', 'Pembayaran vendor berhasil diperbarui.');
    }

    /**
     * HAPUS PEMBAYARAN
     */
    public function destroy($id)
    {
        VendorPayment::findOrFail($id)->delete();

        return back()->with('success', 'Pembayaran vendor berhasil dihapus.');
    }
}
