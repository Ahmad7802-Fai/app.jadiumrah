<?php

namespace App\Http\Controllers\Ticketing;

use App\Http\Controllers\Controller;
use App\Models\TicketInvoice;
use App\Services\Ticketing\TicketPaymentService;
use Illuminate\Http\Request;

class TicketPaymentController extends Controller
{
    public function store(
        Request $request,
        TicketInvoice $invoice,
        TicketPaymentService $service
    ) {
        $data = $request->validate([
            'amount' => 'required|integer|min:1',
            'method' => 'required|string',
            'bank'   => 'nullable|string',
            'receipt'=> 'nullable|file',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')
                ->store('payments', 'public');
        }

        $service->pay(
            $invoice,
            $data['amount'],
            auth()->id(),
            $data['method'],
            $data['bank'] ?? null,
            $receiptPath
        );

        return back()->with('success', 'Pembayaran berhasil');
    }
}


// namespace App\Http\Controllers\Ticketing;

// use App\Http\Controllers\Controller;
// use App\Models\TicketInvoice;
// use App\Services\Ticketing\TicketPaymentService;
// use Illuminate\Http\Request;

// class TicketPaymentController extends Controller
// {
//     public function __construct(
//         protected TicketPaymentService $service
//     ) {}

//     public function store(Request $request, TicketInvoice $invoice)
//     {
//         $this->authorize('pay', $invoice);

//         $data = $request->validate([
//             'amount'  => 'required|integer|min:1',
//             'method'  => 'required|in:TRANSFER,CASH,VA',
//             'bank'    => 'nullable|string|max:50',
//             'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
//         ]);

//         $path = null;
//         if ($request->hasFile('receipt')) {
//             $path = $request->file('receipt')
//                 ->store('payments', 'public');
//         }

//         app(\App\Services\Ticketing\TicketPaymentService::class)
//             ->pay(
//                 $invoice,
//                 $data['amount'],
//                 auth()->id(),
//                 $data['method'],
//                 $data['bank'],
//                 $path
//             );

//         return back()->with('success', 'Payment berhasil dicatat');
//     }

// }

// namespace App\Http\Controllers\Ticketing;

// use App\Http\Controllers\Controller;
// use App\Models\TicketInvoice;
// use App\Services\Ticketing\TicketPaymentService;
// use Illuminate\Http\Request;

// class TicketPaymentController extends Controller
// {
//     public function __construct(
//         protected TicketPaymentService $service
//     ) {}

//     public function store(Request $request, TicketInvoice $invoice)
//     {
//         $this->authorize('pay', $invoice);

//         $request->validate([
//             'amount' => 'required|numeric|min:1000'
//         ]);

//         $this->service->pay($invoice->id, $request->amount);

//         return back()->with('success', 'Pembayaran berhasil');
//     }

// }
