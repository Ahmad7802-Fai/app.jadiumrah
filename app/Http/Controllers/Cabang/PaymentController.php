<?php

namespace App\Http\Controllers\Cabang;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentService;
use App\Models\Jamaah;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /* =====================================================
     | STORE PAYMENT — CABANG
     | - STATUS: PENDING
     | - MENUNGGU APPROVAL PUSAT
     ===================================================== */
    public function store(Request $request, PaymentService $paymentService)
    {
        $ctx = app('access.context'); // branch_id sudah tervalidasi middleware

        $validated = $request->validate([
            'tanggal_bayar'  => ['required', 'date'],
            'jumlah'         => ['required', 'integer', 'min:10000'],
            'metode'         => ['required', 'in:transfer,cash,kantor'],
            'keterangan'     => ['nullable', 'string', 'max:255'],
            'bukti_transfer' => ['nullable', 'image', 'max:2048'],
        ]);

        // 🔐 CABANG hanya boleh input jamaah cabangnya
        $jamaah = Jamaah::withoutGlobalScopes()
            ->where('id', $request->route('jamaah'))
            ->where('branch_id', $ctx['branch_id'])
            ->firstOrFail();

        try {
            $paymentService->input([
                'jamaah_id'      => $jamaah->id,
                'tanggal_bayar'  => $validated['tanggal_bayar'],
                'jumlah'         => $validated['jumlah'],
                'metode'         => $validated['metode'],
                'keterangan'     => $validated['keterangan']
                    ?? 'Input pembayaran oleh cabang',
                'bukti_transfer' => $request->file('bukti_transfer'),
            ], auth()->id());

        } catch (\Throwable $e) {
            return back()->with(
                'warning',
                $e->getMessage()
            );
        }

        return back()->with(
            'success',
            'Pembayaran berhasil dikirim dan menunggu approval pusat.'
        );
    }
}

// namespace App\Http\Controllers\Cabang;

// use App\Http\Controllers\Controller;
// use App\Services\PaymentService;
// use App\Models\Jamaah;
// use Illuminate\Http\Request;

// class PaymentController extends Controller
// {
//     public function store(Request $request, PaymentService $paymentService)
//     {
//         $ctx = app('access.context'); // branch_id sudah valid

//         $request->validate([
//             'tanggal_bayar'  => 'required|date',
//             'jumlah'         => 'required|integer|min:10000',
//             'metode'         => 'required|string|max:50',
//             'keterangan'     => 'nullable|string|max:255',
//             'bukti_transfer' => 'nullable|image|max:2048',
//         ]);

//         // 🔐 CABANG hanya boleh jamaah cabangnya
//         $jamaah = Jamaah::where('id', $request->route('jamaah'))
//             ->where('branch_id', $ctx['branch_id'])
//             ->firstOrFail();

//         $paymentService->input([
//             'jamaah_id'      => $jamaah->id,
//             'tanggal_bayar'  => $request->tanggal_bayar,
//             'jumlah'         => $request->jumlah,
//             'metode'         => $request->metode,
//             'keterangan'     => $request->keterangan ?? 'Input oleh cabang',
//             'bukti_transfer' => $request->file('bukti_transfer'),
//         ], auth()->id());

//         return back()->with(
//             'success',
//             'Pembayaran berhasil dikirim dan menunggu approval pusat.'
//         );
//     }
// }

// namespace App\Http\Controllers\Cabang;

// use App\Http\Controllers\Controller;
// use App\Models\Jamaah;
// use App\Services\PaymentService;
// use Illuminate\Http\Request;

// class PaymentController extends Controller
// {
//     public function __construct(
//         protected PaymentService $service
//     ) {}

//     /* =====================================================
//      | STORE PAYMENT (CABANG INPUT ONLY)
//      ===================================================== */
//     public function store(Request $request, Jamaah $jamaah)
//     {
//         $this->authorize('create', \App\Models\Payments::class);
//         abort_if(
//             auth()->user()->branch_id !== $jamaah->branch_id,
//             403
//         );

//         $data = $request->validate([
//             'tanggal_bayar'   => 'required|date',
//             'jumlah'          => 'required|integer|min:10000',
//             'metode'          => 'required|string|max:50',
//             'keterangan'      => 'nullable|string|max:255',
//             'bukti_transfer'  => 'nullable|image|max:2048',
//         ]);

//         $this->service->input([
//             ...$data,
//             'jamaah_id' => $jamaah->id,
//         ], auth()->id());

//         return redirect()
//             ->route('cabang.jamaah.show', $jamaah)
//             ->with('success', 'Pembayaran berhasil diinput dan menunggu approval pusat.');
//     }

// }
