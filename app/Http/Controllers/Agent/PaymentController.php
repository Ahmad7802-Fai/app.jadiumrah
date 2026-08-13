<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentService;
use App\Models\Jamaah;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, PaymentService $paymentService)
    {
        // ===============================
        // VALIDATION
        // ===============================
        $validated = $request->validate([
            'tanggal_bayar'  => ['required', 'date'],
            'amount'         => ['required', 'integer', 'min:10000'],
            'metode'         => ['required', 'in:transfer,cash'],
            'keterangan'     => ['nullable', 'string'],
            'bukti_transfer' => ['nullable', 'file', 'max:2048'],
        ]);

        // ===============================
        // LOAD JAMAAH (NO GLOBAL SCOPE)
        // ===============================
        $jamaah = Jamaah::withoutGlobalScopes()
            ->findOrFail($request->route('jamaah'));

        // ===============================
        // OWNERSHIP CHECK
        // ===============================
        if (
            !auth()->user()->isAgent() ||
            (int) $jamaah->agent_id !== (int) auth()->user()->agent_id
        ) {
            return back()->with(
                'warning',
                'Jamaah bukan milik Anda.'
            );
        }

        // ===============================
        // TABUNGAN GUARD
        // ===============================
        if ($jamaah->tipe_jamaah === 'tabungan') {
            return back()->with(
                'warning',
                'Jamaah tipe TABUNGAN tidak bisa input pembayaran.'
            );
        }

        // ===============================
        // PAYMENT INPUT
        // ===============================
        try {

            $paymentService->input([
                'jamaah_id'     => $jamaah->id,
                'tanggal_bayar' => $validated['tanggal_bayar'],
                'jumlah'        => $validated['amount'],
                'metode'        => $validated['metode'],
                'keterangan'    => $validated['keterangan'] ?? 'Input dari agent',
                'bukti_transfer'=> $request->file('bukti_transfer'),
            ], auth()->id());

        } catch (\Throwable $e) {

            report($e);

            return back()->with(
                'error',
                'Gagal input pembayaran: ' . $e->getMessage()
            );
        }

        return back()->with(
            'success',
            'Pembayaran berhasil dikirim dan menunggu approval pusat.'
        );
    }
}

// namespace App\Http\Controllers\Agent;

// use App\Http\Controllers\Controller;
// use App\Services\PaymentService;
// use App\Models\Jamaah;
// use Illuminate\Http\Request;

// class PaymentController extends Controller
// {
//     public function store(Request $request, PaymentService $paymentService)
//     {
//         $validated = $request->validate([
//             'tanggal_bayar'   => ['required', 'date'],
//             'amount'          => ['required', 'integer', 'min:10000'],
//             'metode'          => ['required', 'in:transfer,cash'],
//             'keterangan'      => ['nullable', 'string'],
//             'bukti_transfer'  => ['nullable', 'file'],
//         ]);

//         $jamaah = Jamaah::withoutGlobalScopes()
//             ->findOrFail($request->route('jamaah'));

//         // 🔐 ownership
//         if ((int) $jamaah->agent_id !== (int) auth()->user()->agent->id) {
//             return back()->with('warning', 'Jamaah bukan milik Anda.');
//         }

//         // ℹ️ UX helper — jamaah tabungan
//         if ($jamaah->tipe_jamaah === 'tabungan') {
//             return back()->with(
//                 'warning',
//                 'Jamaah dengan tipe TABUNGAN hanya bisa melakukan Top Up Tabungan.'
//             );
//         }

//         try {
//             $paymentService->inputFromAgent(
//                 jamaahId: $jamaah->id,
//                 amount: $validated['amount'],
//                 label: $validated['keterangan'] ?? '',
//                 agentId: auth()->user()->agent->id,
//                 buktiTransfer: $request->file('bukti_transfer')
//             );

//         } catch (\Throwable $e) {
//             return back()->with('warning', $e->getMessage());
//         }

//         return back()->with(
//             'success',
//             'Pembayaran berhasil dikirim dan menunggu approval pusat.'
//         );
//     }
// }

// namespace App\Http\Controllers\Agent;

// use App\Http\Controllers\Controller;
// use App\Services\PaymentService;
// use App\Models\Jamaah;
// use Illuminate\Http\Request;

// class PaymentController extends Controller
// {
//     public function store(Request $request, PaymentService $paymentService)
//     {
//         $validated = $request->validate([
//             'tanggal_bayar'   => ['required', 'date'],
//             'amount'          => ['required', 'integer', 'min:10000'],
//             'metode'          => ['required', 'in:transfer,cash'],
//             'keterangan'      => ['nullable', 'string'],
//             'bukti_transfer'  => ['nullable', 'file'],
//         ]);

//         $jamaah = Jamaah::withoutGlobalScopes()
//             ->findOrFail($request->route('jamaah'));

//         // 🔐 ownership
//         if ((int) $jamaah->agent_id !== (int) auth()->user()->agent->id) {
//             return back()->with('warning', 'Jamaah bukan milik Anda.');
//         }

//         try {
//             $paymentService->inputFromAgent(
//                 jamaahId: $jamaah->id,
//                 amount: $validated['amount'],
//                 label: $validated['keterangan'] ?? '',
//                 agentId: auth()->user()->agent->id,
//                 buktiTransfer: $request->file('bukti_transfer')
//             );

//         } catch (\Throwable $e) {
//             return back()->with('warning', $e->getMessage());
//         }

//         return back()->with(
//             'success',
//             'Pembayaran berhasil dikirim dan menunggu approval pusat.'
//         );
//     }
// }

// namespace App\Http\Controllers\Agent;

// use App\Http\Controllers\Controller;
// use App\Services\PaymentService;
// use App\Models\Jamaah;
// use Illuminate\Http\Request;

// class PaymentController extends Controller
// {
//     public function store(Request $request, PaymentService $paymentService)
//     {
//         $validated = $request->validate([
//             'tanggal_bayar'   => ['required', 'date'],
//             'amount'          => ['required', 'integer', 'min:10000'],
//             'metode'          => ['required', 'in:transfer,cash'],
//             'keterangan'      => ['nullable', 'string'],
//             'bukti_transfer'  => ['nullable', 'file'],
//         ]);

//         $jamaah = Jamaah::withoutGlobalScopes()
//             ->findOrFail($request->route('jamaah'));

//         // 🔐 ownership ringan saja
//         if ((int)$jamaah->agent_id !== (int)auth()->user()->agent->id) {
//             return back()->with('warning', 'Jamaah bukan milik Anda.');
//         }

//         try {
//             $paymentService->input([
//                 'jamaah_id'      => $jamaah->id,
//                 'tanggal_bayar'  => $validated['tanggal_bayar'],
//                 'jumlah'         => $validated['amount'],
//                 'metode'         => $validated['metode'],
//                 'keterangan'     => $validated['keterangan'] ?? 'Input oleh agent',
//                 'bukti_transfer' => $request->file('bukti_transfer'),
//             ], auth()->id());

//         } catch (\Throwable $e) {
//             return back()->with('warning', $e->getMessage());
//         }

//         return back()->with(
//             'success',
//             'Pembayaran berhasil dikirim dan menunggu approval pusat.'
//         );
//     }
// }
