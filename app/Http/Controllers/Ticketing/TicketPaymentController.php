<?php

namespace App\Http\Controllers\Ticketing;

use App\Http\Controllers\Controller;
use App\Models\TicketInvoice;
use App\Services\Ticketing\TicketPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

class TicketPaymentController extends Controller
{
    public function store(
        Request $request,
        TicketInvoice $invoice,
        TicketPaymentService $service
    ) {
        $this->authorize('pay', $invoice);

        $data = $request->validate([
            'amount' => 'required|integer|min:1',
            'method' => 'required|in:TRANSFER,CASH,VA',
            'bank' => 'nullable|string|max:50',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')
                ->store('payments', 'public');
        }

        try {
            $service->pay(
                $invoice,
                $data['amount'],
                auth()->id(),
                $data['method'],
                $data['bank'] ?? null,
                $receiptPath
            );
        } catch (Throwable $exception) {
            if ($receiptPath !== null) {
                Storage::disk('public')->delete($receiptPath);
            }

            throw $exception;
        }

        return back()->with('success', 'Pembayaran berhasil');
    }
}
