<?php
namespace App\Http\Controllers\Layanan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LayananPayment;
use App\Models\LayananInvoice;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'layanan_invoice_id' => 'required|integer|exists:layanan_invoices,id',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'nullable|string',
            'reference_no' => 'nullable|string',
            'catatan' => 'nullable|string'
        ]);

        return DB::transaction(function() use ($data) {

            $invoice = LayananInvoice::findOrFail($data['layanan_invoice_id']);

            LayananPayment::create([
                'layanan_invoice_id' => $invoice->id,
                'amount' => $data['amount'],
                'currency' => $invoice->currency,
                'payment_method' => $data['payment_method'] ?? null,
                'reference_no' => $data['reference_no'] ?? null,
                'catatan' => $data['catatan'] ?? null,
                'status' => 'approved'
            ]);

            // update auto status unpaid → partial → paid
            $invoice->recalcStatus();

            return response()->json(['success' => true]);
        });
    }
}
