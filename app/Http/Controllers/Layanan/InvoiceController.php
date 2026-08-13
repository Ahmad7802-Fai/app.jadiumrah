<?php
namespace App\Http\Controllers\Layanan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\LayananInvoice;
use App\Models\LayananTransaksi;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = LayananInvoice::with('transaksi.client')->orderBy('id', 'desc')->get();
        return view('layanan.invoice.index', compact('invoices'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'layanan_id' => 'required|integer|exists:layanan_transaksi,id',
            'due_date' => 'nullable|date'
        ]);

        $transaksi = LayananTransaksi::findOrFail($data['layanan_id']);

        $invoice = LayananInvoice::create([
            'no_invoice' => 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
            'layanan_id' => $transaksi->id,
            'amount' => $transaksi->subtotal,
            'currency' => 'IDR',
            'due_date' => $data['due_date'] ?? null,
            'status' => 'unpaid',
        ]);

        $transaksi->update(['status' => 'invoiced']);

        return response()->json(['success' => true, 'invoice_id' => $invoice->id]);
    }

    public function show($id)
    {
        $invoice = LayananInvoice::with([
            'transaksi.client',
            'transaksi.items.layananItem',
            'payments'
        ])->findOrFail($id);

        return response()->json($invoice);
    }
}
