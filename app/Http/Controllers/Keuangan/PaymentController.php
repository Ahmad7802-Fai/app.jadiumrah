<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// MODELS
use App\Models\Payments;
use App\Models\PaymentLogs;
use App\Models\Jamaah;
use App\Models\Invoices;

// SERVICES (✅ FIXED)
use App\Services\Payment\PaymentService;

// REQUESTS
use App\Http\Requests\Keuangan\StorePaymentRequest;
use App\Http\Requests\Keuangan\UpdatePaymentRequest;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $service
    ) {}

    /* ============================================================
     | INDEX
     ============================================================ */
    
    public function index(Request $request)
    {
        $payments = Payments::with(['jamaah', 'invoice'])
            ->where('is_deleted', 0)

            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->q;

                $query->where(function ($sub) use ($q) {

                    // 🔎 CARI DI JAMAAH (JIKA ADA)
                    $sub->whereHas('jamaah', function ($j) use ($q) {
                        $j->where('nama_lengkap', 'like', "%{$q}%")
                        ->orWhere('no_id', 'like', "%{$q}%");
                    })

                    // 🔎 ATAU CARI DI PAYMENT (NON JAMAAH)
                    ->orWhere('metode', 'like', "%{$q}%")
                    ->orWhere('jumlah', 'like', "%{$q}%");
                });
            })

            ->when(
                $request->filled('status'),
                fn ($q) => $q->where('status', $request->status)
            )

            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('keuangan.payments.index', compact('payments'));
    }

    // public function index(Request $request)
    // {
    //     $payments = Payments::with(['jamaah','invoice'])
    //         ->where('is_deleted', 0)
    //         ->when($request->filled('q'), function ($q) use ($request) {
    //             $q->whereHas('jamaah', function ($j) use ($request) {
    //                 $j->where('nama_lengkap', 'like', "%{$request->q}%")
    //                   ->orWhere('no_id', 'like', "%{$request->q}%");
    //             });
    //         })
    //         ->when($request->filled('status'), fn ($q) =>
    //             $q->where('status', $request->status)
    //         )
    //         ->orderByDesc('created_at')
    //         ->paginate(20)
    //         ->withQueryString();

    //     return view('keuangan.payments.index', compact('payments'));
    // }

    /* ============================================================
     | CREATE
     ============================================================ */
    public function create(Request $request)
    {
        if ($request->filled('invoice_id')) {
            $invoice = Invoices::with('jamaah')->findOrFail($request->invoice_id);

            return view('keuangan.payments.create', [
                'mode'          => 'cicilan',
                'invoice'       => $invoice,
                'jamaah'        => $invoice->jamaah,
                'total_tagihan' => $invoice->total_tagihan,
                'sisa_tagihan'  => $invoice->sisa_tagihan,
            ]);
        }

        return view('keuangan.payments.create', [
            'mode' => 'baru',
        ]);
    }

    /* ============================================================
     | STORE — INPUT PAYMENT (PENDING)
     ============================================================ */
    public function store(StorePaymentRequest $request)
    {
        try {
            $payment = $this->service->input(
                $request->validated(),
                Auth::id()
            );

            return redirect()
                ->route('keuangan.payments.show', $payment->id)
                ->with('success', 'Pembayaran berhasil dicatat (pending).');

        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /* ============================================================
     | SHOW
     ============================================================ */
    public function show(int $id)
    {
        $payment = Payments::with(['jamaah','invoice'])->findOrFail($id);

        $history = $payment->invoice
            ? Payments::where('invoice_id', $payment->invoice->id)
                ->where('status', Payments::STATUS_VALID)
                ->where('is_deleted', 0)
                ->orderBy('tanggal_bayar')
                ->get()
            : collect();

        $logs = PaymentLogs::where('payment_id', $payment->id)
            ->orderByDesc('created_at')
            ->get();

        return view('keuangan.payments.show', compact(
            'payment','history','logs'
        ));
    }

    /* ============================================================
    | APPROVE — KEUANGAN ONLY
    ============================================================ */
    public function approve(int $id)
    {
        $payment = Payments::findOrFail($id);

        $this->authorize('approve', $payment);

        // 1️⃣ APPROVE PAYMENT (TRANSAKSI AMAN)
        $payment = $this->service->approve(
            $payment,
            auth()->id()
        );

        // 2️⃣ GENERATE KOMISI (SIDE EFFECT — HARUS DI CONTROLLER)
        try {
            app(\App\Services\Komisi\KomisiService::class)
                ->generateFromPayment(
                    $payment->jamaah,
                    $payment
                );
        } catch (\Throwable $e) {

            // ❗ Komisi gagal TIDAK boleh membatalkan approve
            logger()->error('GAGAL GENERATE KOMISI AGENT', [
                'payment_id' => $payment->id,
                'jamaah_id'  => $payment->jamaah_id,
                'error'      => $e->getMessage(),
            ]);
        }

        return back()->with(
            'success',
            'Pembayaran berhasil divalidasi.'
        );
    }


    /* ============================================================
     | REJECT — KEUANGAN ONLY
     ============================================================ */
    public function reject(Request $request, int $id)
    {
        $request->validate([
            'reason' => ['required','string','min:5'],
        ]);

        $payment = Payments::findOrFail($id);

        $this->authorize('reject', $payment);

        $this->service->reject(
            $payment,
            Auth::id(),
            $request->reason
        );

        return back()->with('success', 'Pembayaran berhasil ditolak.');
    }

    /* ============================================================
     | EDIT — PENDING ONLY
     ============================================================ */
    public function edit(int $id)
    {
        $payment = Payments::with('jamaah')->findOrFail($id);

        abort_if(
            $payment->status !== Payments::STATUS_PENDING,
            403,
            'Hanya payment pending yang bisa diedit'
        );

        return view('keuangan.payments.edit', compact('payment'));
    }

    /* ============================================================
     | UPDATE — PENDING ONLY
     ============================================================ */
    public function update(UpdatePaymentRequest $request, int $id)
    {
        $payment = Payments::findOrFail($id);

        abort_if(
            $payment->status !== Payments::STATUS_PENDING,
            403,
            'Hanya payment pending yang bisa diupdate'
        );

        $payment->update($request->validated());

        // 🧠 logging otomatis via Observer
        return redirect()
            ->route('keuangan.payments.show', $payment->id)
            ->with('success', 'Pembayaran berhasil diperbarui.');
    }

    /* ============================================================
     | DELETE — SOFT DELETE (PENDING ONLY)
     ============================================================ */
    public function destroy(int $id)
    {
        $payment = Payments::findOrFail($id);

        abort_if(
            $payment->status !== Payments::STATUS_PENDING,
            403,
            'Hanya payment pending yang bisa dihapus'
        );

        $this->service->softDelete($payment, Auth::id());

        return redirect()
            ->route('keuangan.payments.index')
            ->with('success', 'Pembayaran berhasil dihapus.');
    }

    public function printKwitansiPremium(int $id)
    {
        $payment = Payments::with(['jamaah','invoice'])->findOrFail($id);

        abort_if($payment->status !== 'valid', 403);
        abort_if(! $payment->invoice, 500);

        $invoice = $payment->invoice;
        $jamaah  = $payment->jamaah;

        $history = Payments::where('invoice_id', $invoice->id)
            ->where('status', 'valid')
            ->where('is_deleted', 0)
            ->orderBy('tanggal_bayar')
            ->get();

        $qrRaw = \QrCode::format('svg')
            ->size(120)
            ->errorCorrection('H')
            ->generate($invoice->nomor_invoice);

        $qrCode = base64_encode($qrRaw);

        $pdf = \PDF::loadView(
            'keuangan.payments.print-premium',
            compact('payment','invoice','jamaah','history','qrCode')
        )->setPaper('A4','portrait');

        return $pdf->stream("kwitansi-{$payment->id}.pdf");
    }

    /* ============================================================
     | AJAX — SEARCH JAMAAH (SELECT2)
     ============================================================ */
    public function searchJamaah(Request $request)
    {
        $q = trim($request->get('q',''));

        if (strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $jamaah = Jamaah::where('nama_lengkap','like',"%{$q}%")
            ->orWhere('no_id','like',"%{$q}%")
            ->limit(20)
            ->get();

        return response()->json([
            'results' => $jamaah->map(fn ($j) => [
                'id'   => $j->id,
                'text' => "{$j->nama_lengkap} ({$j->no_id})",
            ]),
        ]);
    }

    /* ============================================================
     | AJAX — INVOICE INFO (READ ONLY)
     ============================================================ */
    public function ajaxInvoice(int $jamaah_id)
    {
        $jamaah = Jamaah::findOrFail($jamaah_id);

        $invoice = Invoices::where('jamaah_id', $jamaah->id)
            ->whereIn('status',['belum_lunas','cicilan'])
            ->orderBy('id')
            ->first();

        return response()->json([
            'invoice' => $invoice,
            'rekomendasi_total_tagihan' => $jamaah->harga_akhir,
        ]);
    }
}


// namespace App\Http\Controllers\Keuangan;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use App\Models\Payments;
// use App\Models\PaymentLogs;
// use App\Models\Jamaah;
// use App\Models\Invoices;
// use App\Services\Payment\PaymentService;
// use App\Http\Requests\Keuangan\StorePaymentRequest;
// use App\Http\Requests\Keuangan\UpdatePaymentRequest;

// class PaymentController extends Controller
// {
//     protected PaymentService $service;

//     public function __construct(PaymentService $service)
//     {
//         $this->service = $service;
//     }

//     /* ============================================================
//      | INDEX
//      ============================================================ */
//     public function index(Request $request)
//     {
//         $query = Payments::with(['jamaah','invoice'])
//             ->where('is_deleted', 0)
//             ->orderByDesc('created_at');

//         if ($request->filled('q')) {
//             $q = $request->q;
//             $query->whereHas('jamaah', function ($j) use ($q) {
//                 $j->where('nama_lengkap', 'like', "%{$q}%")
//                   ->orWhere('no_id', 'like', "%{$q}%");
//             });
//         }

//         if ($request->filled('status')) {
//             $query->where('status', $request->status);
//         }

//         $payments = $query->paginate(20)->withQueryString();

//         return view('keuangan.payments.index', compact('payments'));
//     }

//     /* ============================================================
//      | CREATE
//      | - Mode BARU (AJAX)
//      | - Mode CICILAN (via invoice_id)
//      ============================================================ */
//     public function create(Request $request)
//     {
//         if ($request->filled('invoice_id')) {
//             $invoice = Invoices::with('jamaah')->findOrFail($request->invoice_id);

//             return view('keuangan.payments.create', [
//                 'mode'          => 'cicilan',
//                 'invoice'       => $invoice,
//                 'jamaah'        => $invoice->jamaah,
//                 'total_tagihan' => $invoice->total_tagihan,
//                 'sisa_tagihan'  => $invoice->sisa_tagihan,
//             ]);
//         }

//         return view('keuangan.payments.create', [
//             'mode' => 'baru',
//         ]);
//     }

//     /* ============================================================
//      | STORE — INPUT PAYMENT (PENDING)
//      ============================================================ */
//     public function store(StorePaymentRequest $request)
//     {
//         try {
//             $payment = $this->service->input(
//                 $request->validated(),
//                 Auth::id()
//             );

//             return redirect()
//                 ->route('keuangan.payments.show', $payment->id)
//                 ->with('success', 'Pembayaran berhasil dicatat (pending).');

//         } catch (\Throwable $e) {
//             return back()->withInput()->with('error', $e->getMessage());
//         }
//     }

//     /* ============================================================
//      | SHOW
//      ============================================================ */
//     public function show(int $id)
//     {
//         $payment = Payments::with(['jamaah','invoice'])->findOrFail($id);

//         $history = collect();
//         if ($payment->invoice) {
//             $history = Payments::where('invoice_id', $payment->invoice->id)
//                 ->where('status', 'valid')
//                 ->where('is_deleted', 0)
//                 ->orderBy('tanggal_bayar')
//                 ->get();
//         }

//         $logs = PaymentLogs::where('payment_id', $payment->id)
//             ->orderByDesc('created_at')
//             ->get();

//         return view('keuangan.payments.show', compact(
//             'payment','history','logs'
//         ));
//     }

//     /* ============================================================
//      | APPROVE — POLICY + SERVICE
//      ============================================================ */
//     public function approve(int $id)
//     {
//         $payment = Payments::findOrFail($id);

//         $this->authorize('approve', $payment);

//         $this->service->approve($payment, Auth::id());

//         return back()->with('success', 'Pembayaran berhasil divalidasi.');
//     }

//     /* ============================================================
//      | REJECT — POLICY + SERVICE
//      ============================================================ */
//     public function reject(Request $request, int $id)
//     {
//         $request->validate([
//             'reason' => ['required','string','min:5'],
//         ]);

//         $payment = Payments::findOrFail($id);

//         $this->authorize('reject', $payment);

//         $this->service->reject(
//             $payment,
//             Auth::id(),
//             $request->reason
//         );

//         return back()->with('success', 'Pembayaran berhasil ditolak.');
//     }

//     /* ============================================================
//      | EDIT — PENDING ONLY
//      ============================================================ */
//     public function edit(int $id)
//     {
//         $payment = Payments::with('jamaah')->findOrFail($id);

//         abort_if($payment->status !== 'pending', 403);

//         return view('keuangan.payments.edit', compact('payment'));
//     }

//     /* ============================================================
//      | UPDATE — PENDING ONLY
//      ============================================================ */
//     public function update(UpdatePaymentRequest $request, int $id)
//     {
//         $payment = Payments::findOrFail($id);

//         abort_if($payment->status !== 'pending', 403);

//         $payment->update($request->validated());

//         PaymentLogs::create([
//             'payment_id' => $payment->id,
//             'action'     => 'update',
//             'meta'       => json_encode($request->validated()),
//             'created_by' => Auth::id(),
//         ]);

//         return redirect()
//             ->route('keuangan.payments.show', $payment->id)
//             ->with('success', 'Pembayaran diperbarui.');
//     }

//     /* ============================================================
//      | DELETE — SOFT DELETE (PENDING ONLY)
//      ============================================================ */
//     public function destroy(int $id)
//     {
//         $payment = Payments::findOrFail($id);

//         abort_if($payment->status !== 'pending', 403);

//         $this->service->softDelete($payment, Auth::id());

//         return redirect()
//             ->route('keuangan.payments.index')
//             ->with('success', 'Pembayaran berhasil dihapus.');
//     }

//     /* ============================================================
//      | PRINT KWITANSI — VALID ONLY
//      ============================================================ */
//     public function printKwitansiPremium(int $id)
//     {
//         $payment = Payments::with(['jamaah','invoice'])->findOrFail($id);

//         abort_if($payment->status !== 'valid', 403);
//         abort_if(! $payment->invoice, 500);

//         $invoice = $payment->invoice;
//         $jamaah  = $payment->jamaah;

//         $history = Payments::where('invoice_id', $invoice->id)
//             ->where('status', 'valid')
//             ->where('is_deleted', 0)
//             ->orderBy('tanggal_bayar')
//             ->get();

//         $qrRaw = \QrCode::format('svg')
//             ->size(120)
//             ->errorCorrection('H')
//             ->generate($invoice->nomor_invoice);

//         $qrCode = base64_encode($qrRaw);

//         $pdf = \PDF::loadView(
//             'keuangan.payments.print-premium',
//             compact('payment','invoice','jamaah','history','qrCode')
//         )->setPaper('A4','portrait');

//         return $pdf->stream("kwitansi-{$payment->id}.pdf");
//     }

//     /* ============================================================
//      | AJAX — SEARCH JAMAAH (SELECT2)
//      ============================================================ */
//     public function searchJamaah(Request $request)
//     {
//         $q = trim($request->get('q',''));

//         if (strlen($q) < 2) {
//             return response()->json(['results' => []]);
//         }

//         $jamaah = Jamaah::where('nama_lengkap','like',"%{$q}%")
//             ->orWhere('no_id','like',"%{$q}%")
//             ->limit(20)
//             ->get();

//         return response()->json([
//             'results' => $jamaah->map(fn ($j) => [
//                 'id'   => $j->id,
//                 'text' => "{$j->nama_lengkap} ({$j->no_id})",
//             ]),
//         ]);
//     }

//     /* ============================================================
//      | AJAX — INVOICE INFO (READ ONLY)
//      ============================================================ */
//     public function ajaxInvoice(int $jamaah_id)
//     {
//         $jamaah = Jamaah::findOrFail($jamaah_id);

//         $invoice = Invoices::where('jamaah_id', $jamaah->id)
//             ->whereIn('status',['belum_lunas','cicilan'])
//             ->orderBy('id')
//             ->first();

//         return response()->json([
//             'invoice' => $invoice,
//             'rekomendasi_total_tagihan' => $jamaah->harga_akhir,
//         ]);
//     }
// }

// namespace App\Http\Controllers\Keuangan;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use App\Models\Payments;
// use App\Models\PaymentLogs;
// use App\Services\PaymentService;
// use App\Http\Requests\Keuangan\StorePaymentRequest;
// use App\Http\Requests\Keuangan\UpdatePaymentRequest;
// use App\Models\Jamaah;
// use App\Models\Invoices;
// class PaymentController extends Controller
// {
//     protected PaymentService $service;

//     public function __construct(PaymentService $service)
//     {
//         $this->service = $service;
//     }

//     /* ============================================================
//      | INDEX
//      ============================================================ */
//     public function index(Request $request)
//     {
//         $query = Payments::with(['jamaah', 'invoice'])
//             ->where('is_deleted', 0)
//             ->orderByDesc('created_at');

//         if ($request->filled('q')) {
//             $q = $request->q;
//             $query->whereHas('jamaah', function ($j) use ($q) {
//                 $j->where('nama_lengkap', 'like', "%{$q}%")
//                   ->orWhere('no_id', 'like', "%{$q}%");
//             });
//         }

//         if ($request->filled('status')) {
//             $query->where('status', $request->status);
//         }

//         $payments = $query->paginate(20)->withQueryString();

//         return view('keuangan.payments.index', compact('payments'));
//     }

//     /* ============================================================
//      | CREATE
//      ============================================================ */
//     public function create(Request $request)
//     {
//         // Mode cicilan → dari invoice
//         if ($request->filled('invoice_id')) {
//             $invoice = \App\Models\Invoices::with('jamaah')
//                 ->findOrFail($request->invoice_id);

//             return view('keuangan.payments.create', [
//                 'mode'          => 'cicilan',
//                 'invoice'       => $invoice,
//                 'jamaah'        => $invoice->jamaah,
//                 'total_tagihan' => $invoice->total_tagihan,
//                 'sisa_tagihan'  => $invoice->sisa_tagihan,
//             ]);
//         }

//         // Mode pembayaran baru
//         return view('keuangan.payments.create', [
//             'mode' => 'baru',
//         ]);
//     }

//     /* ============================================================
//      | STORE (INPUT → PENDING)
//      ============================================================ */
//     public function store(StorePaymentRequest $request)
//     {
//         $payment = $this->service->input(
//             $request->validated(),
//             Auth::id()
//         );

//         return redirect()
//             ->route('keuangan.payments.show', $payment->id)
//             ->with('success', 'Pembayaran berhasil dicatat (pending).');
//     }

//     /* ============================================================
//      | SHOW
//      ============================================================ */
//     public function show(int $id)
//     {
//         $payment = Payments::with(['jamaah', 'invoice'])->findOrFail($id);

//         $history = collect();
//         if ($payment->invoice) {
//             $history = Payments::where('invoice_id', $payment->invoice->id)
//                 ->where('status', 'valid')
//                 ->where('is_deleted', 0)
//                 ->orderBy('tanggal_bayar')
//                 ->get();
//         }

//         $logs = PaymentLogs::where('payment_id', $payment->id)
//             ->orderByDesc('created_at')
//             ->get();

//         return view('keuangan.payments.show', compact(
//             'payment',
//             'history',
//             'logs'
//         ));
//     }

//     /* ============================================================
//      | APPROVE (POLICY + SERVICE)
//      ============================================================ */
//     public function approve(int $id)
//     {
//         $payment = Payments::findOrFail($id);

//         // 🔒 Policy guard
//         $this->authorize('approve', $payment);

//         $this->service->approve($payment, Auth::id());

//         return back()->with('success', 'Pembayaran berhasil divalidasi.');
//     }

//     /* ============================================================
//      | REJECT (POLICY + SERVICE)
//      ============================================================ */
//     public function reject(Request $request, int $id)
//     {
//         $request->validate([
//             'reason' => ['required', 'string', 'min:5'],
//         ]);

//         $payment = Payments::findOrFail($id);

//         // 🔒 Policy guard
//         $this->authorize('reject', $payment);

//         $this->service->reject(
//             $payment,
//             Auth::id(),
//             $request->reason
//         );

//         return back()->with('success', 'Pembayaran berhasil ditolak.');
//     }

//     /* ============================================================
//      | EDIT (PENDING ONLY)
//      ============================================================ */
//     public function edit(int $id)
//     {
//         $payment = Payments::with('jamaah')->findOrFail($id);

//         abort_if($payment->status !== 'pending', 403);

//         return view('keuangan.payments.edit', compact('payment'));
//     }

//     /* ============================================================
//      | UPDATE (PENDING ONLY)
//      ============================================================ */
//     public function update(UpdatePaymentRequest $request, int $id)
//     {
//         $payment = Payments::findOrFail($id);

//         abort_if($payment->status !== 'pending', 403);

//         $payment->update($request->validated());

//         PaymentLogs::create([
//             'payment_id' => $payment->id,
//             'action'     => 'update',
//             'meta'       => json_encode($request->validated()),
//             'created_by' => Auth::id(),
//         ]);

//         return redirect()
//             ->route('keuangan.payments.show', $payment->id)
//             ->with('success', 'Pembayaran diperbarui.');
//     }

//     /* ============================================================
//      | DELETE (SOFT DELETE)
//      ============================================================ */
//     public function destroy(int $id)
//     {
//         $payment = Payments::findOrFail($id);

//         abort_if($payment->status !== 'pending', 403);

//         $this->service->softDelete($payment, Auth::id());

//         return redirect()
//             ->route('keuangan.payments.index')
//             ->with('success', 'Pembayaran berhasil dihapus.');
//     }

//     /* ============================================================
//      | PRINT KWITANSI PREMIUM
//      ============================================================ */
//     public function printKwitansiPremium(int $id)
//     {
//         $payment = Payments::with(['jamaah','invoice'])->findOrFail($id);

//         abort_if($payment->status !== 'valid', 403);
//         abort_if(! $payment->invoice, 500);

//         $invoice = $payment->invoice;
//         $jamaah  = $payment->jamaah;

//         $history = Payments::where('invoice_id', $invoice->id)
//             ->where('status', 'valid')
//             ->where('is_deleted', 0)
//             ->orderBy('tanggal_bayar')
//             ->get();

//         // Read-only recalc
//         $invoice->total_terbayar = $history->sum('jumlah');
//         $invoice->sisa_tagihan   = max(0, $invoice->total_tagihan - $invoice->total_terbayar);
//         $invoice->status = $invoice->sisa_tagihan <= 0
//             ? 'lunas'
//             : ($invoice->total_terbayar > 0 ? 'cicilan' : 'belum_lunas');

//         $qrRaw = \QrCode::format('svg')
//             ->size(120)
//             ->errorCorrection('H')
//             ->generate($invoice->nomor_invoice);

//         $qrCode = base64_encode($qrRaw);

//         $pdf = \PDF::loadView(
//             'keuangan.payments.print-premium',
//             compact('payment','invoice','jamaah','history','qrCode')
//         )->setPaper('A4','portrait');

//         return $pdf->stream("kwitansi-{$payment->id}.pdf");
//     }

//     /* ============================================================
//     | AJAX — SEARCH JAMAAH (SELECT2)
//     ============================================================ */
//     public function searchJamaah(Request $request)
//     {
//         $q = trim($request->get('q', ''));

//         if (strlen($q) < 2) {
//             return response()->json(['results' => []]);
//         }

//         $jamaah = Jamaah::where('nama_lengkap', 'like', "%{$q}%")
//             ->orWhere('no_id', 'like', "%{$q}%")
//             ->limit(20)
//             ->get();

//         return response()->json([
//             'results' => $jamaah->map(fn ($j) => [
//                 'id'   => $j->id,
//                 'text' => "{$j->nama_lengkap} ({$j->no_id})",
//             ]),
//         ]);
//     }

//     /* ============================================================
//     | AJAX — INVOICE INFO (READ ONLY)
//     ============================================================ */
//     public function ajaxInvoice(int $jamaah_id)
//     {
//         $invoice = Invoices::where('jamaah_id', $jamaah_id)
//             ->whereIn('status', ['belum_lunas', 'cicilan'])
//             ->orderBy('id', 'asc')
//             ->first();

//         return response()->json([
//             'invoice' => $invoice ? [
//                 'id'             => $invoice->id,
//                 'total_tagihan'  => $invoice->total_tagihan,
//                 'total_terbayar' => $invoice->total_terbayar,
//                 'sisa_tagihan'   => $invoice->sisa_tagihan,
//                 'status'         => $invoice->status,
//             ] : null,
//         ]);
//     }

// }

// namespace App\Http\Controllers\Keuangan;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

// use App\Models\Payments;
// use App\Models\PaymentLogs;

// use App\Services\PaymentService;
// use App\Http\Requests\Keuangan\StorePaymentRequest;
// use App\Http\Requests\Keuangan\UpdatePaymentRequest;

// class PaymentController extends Controller
// {
//     protected PaymentService $service;

//     public function __construct(PaymentService $service)
//     {
//         $this->service = $service;
//     }

//     /* ============================================================
//      | INDEX
//      ============================================================ */
//     public function index(Request $request)
//     {
//         $query = Payments::with(['jamaah', 'invoice'])
//             ->where('is_deleted', 0)
//             ->orderByDesc('created_at');

//         if ($request->filled('q')) {
//             $q = $request->q;
//             $query->whereHas('jamaah', function ($j) use ($q) {
//                 $j->where('nama_lengkap', 'like', "%{$q}%")
//                   ->orWhere('no_id', 'like', "%{$q}%");
//             });
//         }

//         if ($request->filled('status')) {
//             $query->where('status', $request->status);
//         }

//         $payments = $query->paginate(20)->withQueryString();

//         return view('keuangan.payments.index', compact('payments'));
//     }

//     /* ============================================================
//      | CREATE
//      ============================================================ */
//     public function create()
//     {
//         return view('keuangan.payments.create');
//     }

//     /* ============================================================
//      | STORE (DELEGATE TO SERVICE)
//      ============================================================ */
//     public function store(StorePaymentRequest $request)
//     {
//         try {
//             $payment = $this->service->input(
//                 $request->validated(),
//                 Auth::id()
//             );

//             return redirect()
//                 ->route('keuangan.payments.show', $payment->id)
//                 ->with('success', 'Pembayaran berhasil dicatat (pending).');

//         } catch (\Throwable $e) {
//             return back()->withInput()->with('error', $e->getMessage());
//         }
//     }

//     /* ============================================================
//      | SHOW
//      ============================================================ */
//     public function show($id)
//     {
//         $payment = Payments::with(['jamaah', 'invoice'])->findOrFail($id);

//         $history = [];
//         if ($payment->invoice) {
//             $history = Payments::where('invoice_id', $payment->invoice->id)
//                 ->where('status', 'valid')
//                 ->where('is_deleted', 0)
//                 ->orderBy('tanggal_bayar')
//                 ->get();
//         }

//         $logs = PaymentLogs::where('payment_id', $payment->id)
//             ->orderByDesc('created_at')
//             ->get();

//         return view('keuangan.payments.show', compact('payment', 'history', 'logs'));
//     }

//     /* ============================================================
//      | APPROVE (SERVICE ONLY)
//      ============================================================ */
//     public function approve(int $id)
//     {
//         try {
//             $payment = Payments::findOrFail($id);

//             $this->service->approve($payment, Auth::id());

//             return back()->with('success', 'Pembayaran berhasil divalidasi.');

//         } catch (\Throwable $e) {
//             return back()->with('error', $e->getMessage());
//         }
//     }

//     /* ============================================================
//      | REJECT (SERVICE ONLY + AUDIT)
//      ============================================================ */
//     public function reject(Request $request, int $id)
//     {
//         $request->validate([
//             'reason' => ['required', 'string', 'min:5'],
//         ]);

//         try {
//             $payment = Payments::findOrFail($id);

//             $this->service->reject(
//                 $payment,
//                 Auth::id(),
//                 $request->reason
//             );

//             return back()->with('success', 'Pembayaran berhasil ditolak.');

//         } catch (\Throwable $e) {
//             return back()->with('error', $e->getMessage());
//         }
//     }

//     /* ============================================================
//      | EDIT (PENDING ONLY)
//      ============================================================ */
//     public function edit(int $id)
//     {
//         $payment = Payments::with('jamaah')->findOrFail($id);

//         abort_if($payment->status !== 'pending', 403);

//         return view('keuangan.payments.edit', compact('payment'));
//     }

//     /* ============================================================
//      | UPDATE (PENDING ONLY)
//      ============================================================ */
//     public function update(UpdatePaymentRequest $request, int $id)
//     {
//         $payment = Payments::findOrFail($id);

//         abort_if($payment->status !== 'pending', 403);

//         $payment->update($request->validated());

//         PaymentLogs::create([
//             'payment_id' => $payment->id,
//             'action'     => 'update',
//             'meta'       => json_encode($request->validated()),
//             'created_by' => Auth::id(),
//         ]);

//         return redirect()
//             ->route('keuangan.payments.show', $payment->id)
//             ->with('success', 'Pembayaran diperbarui.');
//     }

//     /* ============================================================
//      | DELETE (SOFT DELETE VIA SERVICE)
//      ============================================================ */
//     public function destroy(int $id)
//     {
//         try {
//             $payment = Payments::findOrFail($id);

//             $this->service->softDelete($payment, Auth::id());

//             return redirect()
//                 ->route('keuangan.payments.index')
//                 ->with('success', 'Pembayaran berhasil dihapus.');

//         } catch (\Throwable $e) {
//             return back()->with('error', $e->getMessage());
//         }
//     }

//     /* ============================================================
//      | PRINT KWITANSI PREMIUM
//      ============================================================ */
//     public function printKwitansiPremium(int $id)
//     {
//         $payment = Payments::with(['jamaah','invoice'])->findOrFail($id);

//         abort_if($payment->status !== 'valid', 403,
//             'Kwitansi hanya tersedia untuk pembayaran yang sudah divalidasi.'
//         );

//         abort_if(! $payment->invoice, 500,
//             'Invoice belum terbentuk. Hubungi administrator.'
//         );

//         $invoice = $payment->invoice;
//         $jamaah  = $payment->jamaah;

//         $history = Payments::where('invoice_id', $invoice->id)
//             ->where('status', 'valid')
//             ->where('is_deleted', 0)
//             ->orderBy('tanggal_bayar')
//             ->get();

//         // Read-only recalculation
//         $invoice->total_terbayar = $history->sum('jumlah');
//         $invoice->sisa_tagihan   = max(0, $invoice->total_tagihan - $invoice->total_terbayar);
//         $invoice->status = $invoice->sisa_tagihan <= 0
//             ? 'lunas'
//             : ($invoice->total_terbayar > 0 ? 'cicilan' : 'belum_lunas');

//         $qrRaw = \QrCode::format('svg')
//             ->size(120)
//             ->errorCorrection('H')
//             ->generate($invoice->nomor_invoice);

//         $qrCode = base64_encode($qrRaw);

//         $pdf = \PDF::loadView(
//             'keuangan.payments.print-premium',
//             compact('payment','invoice','jamaah','history','qrCode')
//         )->setPaper('A4','portrait');

//         return $pdf->stream("kwitansi-{$payment->id}.pdf");
//     }
// }


// namespace App\Http\Controllers\Keuangan;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Storage;
// use Carbon\Carbon;

// use App\Http\Requests\Keuangan\StorePaymentRequest;
// use App\Http\Requests\Keuangan\UpdatePaymentRequest;
// use App\Services\PaymentService;
// use Barryvdh\DomPDF\Facade\Pdf;
// use App\Models\Payments;
// use App\Models\Invoices;
// use App\Models\Jamaah;
// use App\Models\PaymentLogs;

// class PaymentController extends Controller
// {
//     protected PaymentService $service;

//     public function __construct(PaymentService $service)
//     {
//         $this->service = $service;
//         // middleware auth sudah di route group (asumsi). Tambahkan policy/middleware jika perlu.
//     }

//     /**
//      * Index - daftar pembayaran (compact + card view)
//      */
//     public function index(Request $request)
//     {
//         $query = Payments::with(['jamaah', 'invoice'])
//             ->where(function ($q) {
//                 $q->whereNull('is_deleted')->orWhere('is_deleted', 0);
//             })
//             ->orderBy('created_at', 'desc');

//         if ($request->filled('q')) {
//             $q = $request->q;
//             $query->whereHas('jamaah', function ($j) use ($q) {
//                 $j->where('nama_lengkap', 'like', "%{$q}%")
//                   ->orWhere('no_id', 'like', "%{$q}%");
//             })->orWhere('jumlah', 'like', "%{$q}%");
//         }

//         if ($request->filled('status')) {
//             $query->where('status', $request->status);
//         }

//         $payments = $query->paginate(20)->appends($request->query());

//         return view('keuangan.payments.index', compact('payments'));
//     }
//     // Ajax Search Jamaah
//     public function searchJamaah(Request $request)
//     {
//         $q = trim($request->q);

//         if (!$q || strlen($q) < 2) {
//             return response()->json(['results' => []]);
//         }

//         /*
//         |--------------------------------------------------------------------------
//         | RULE F7:
//         | Jamaah yang boleh muncul:
//         | 1. Punya invoice BELUM LUNAS atau CICILAN
//         | 2. ATAU Jamaah yang BELUM punya invoice sama sekali
//         |--------------------------------------------------------------------------
//         */

//         $jamaah = \App\Models\Jamaah::query()
//             ->where(function ($x) use ($q) {
//                 $x->where('nama_lengkap', 'LIKE', "%{$q}%")
//                 ->orWhere('no_id', 'LIKE', "%{$q}%");
//             })
//             ->where(function ($w) {
//                 $w->whereHas('invoices', function ($inv) {
//                     $inv->whereIn('status', ['belum_lunas', 'cicilan']);
//                 })
//                 ->orWhereDoesntHave('invoices');
//             })
//             ->limit(20)
//             ->get();

//         $results = $jamaah->map(function ($j) {
//             return [
//                 'id'   => $j->id,
//                 'text' => "{$j->nama_lengkap} ({$j->no_id})"
//             ];
//         });

//         return response()->json([
//             'results' => $results
//         ]);
//     }



//     /**
//      * Create form
//      * - jika invoice_id diberikan -> mode cicilan
//      * - sebaliknya -> mode baru (pilih jamaah tanpa invoice)
//      */
//     public function create(Request $request)
//     {
//         $invoiceId = $request->invoice_id;

//         if ($invoiceId) {
//             $invoice = Invoices::with('jamaah')->findOrFail($invoiceId);

//             return view('keuangan.payments.create', [
//                 'mode' => 'cicilan',
//                 'invoice' => $invoice,
//                 'jamaah' => $invoice->jamaah,
//                 'total_tagihan' => $invoice->total_tagihan,
//                 'total_terbayar' => $invoice->total_terbayar,
//                 'sisa_tagihan' => $invoice->sisa_tagihan,
//             ]);
//         }

//         // default: hanya jamaah yang belum punya invoice (sama seperti implementasi lama)
//         $jamaahTanpaInvoice = Jamaah::whereDoesntHave('invoices')->get();

//         return view('keuangan.payments.create', [
//             'mode' => 'baru',
//             'jamaahTanpaInvoice' => $jamaahTanpaInvoice,
//         ]);
//     }

//     /**
//      * Store payment (delegasi ke PaymentService)
//      */
//     public function store(StorePaymentRequest $request)
//     {
//         $payload = $request->validated();

//         // kirim file sebagai object jika ada, service yang tangani penyimpanan
//         if ($request->hasFile('bukti_transfer')) {
//             $payload['bukti_transfer'] = $request->file('bukti_transfer');
//         }

//         try {
//             $payment = $this->service->store($payload, Auth::user());

//             return redirect()->route('keuangan.payments.show', $payment->id)
//                 ->with('success', 'Pembayaran disimpan dan menunggu validasi.');
//         } catch (\Throwable $e) {
//             return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pembayaran: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Show payment + invoice + history
//      */
//     public function show($id)
//     {
//         $payment = Payments::with(['jamaah', 'invoice'])->findOrFail($id);

//         if (! $payment->invoice) {
//             return back()->with('error', 'Invoice tidak ditemukan untuk pembayaran ini.');
//         }

//         $invoice = $payment->invoice;
//         $jamaah  = $payment->jamaah;

//         $history = Payments::where('invoice_id', $invoice->id)
//             ->where('status', 'valid')
//             ->where(function ($q) {
//                 $q->whereNull('is_deleted')->orWhere('is_deleted', 0);
//             })
//             ->orderBy('tanggal_bayar', 'asc')
//             ->get();

//         $logs = PaymentLogs::where('payment_id', $payment->id)
//             ->orderBy('created_at', 'desc')
//             ->get();

//         $total_terbayar = $history->sum('jumlah');
//         $sisa_tagihan   = max(0, $invoice->total_tagihan - $total_terbayar);

//         // tampilkan recalculated values (tidak wajib menyimpan)
//         $invoice->total_terbayar = $total_terbayar;
//         $invoice->sisa_tagihan = $sisa_tagihan;
//         $invoice->status = $sisa_tagihan <= 0 ? 'lunas' : ($total_terbayar > 0 ? 'cicilan' : 'belum_lunas');

//         return view('keuangan.payments.show', compact(
//             'payment','invoice','jamaah','history','logs','total_terbayar','sisa_tagihan'
//         ));
//     }

//     /**
//      * Edit form
//      */
//     public function edit($id)
//     {
//         $payment = Payments::with(['jamaah','invoice'])->findOrFail($id);
//         return view('keuangan.payments.edit', compact('payment'));
//     }

//     /**
//      * Update payment (basic fields) — file handling & invoice recalc
//      */
//     public function update(UpdatePaymentRequest $request, $id)
//     {
//         $payment = Payments::findOrFail($id);
//         $invoice = $payment->invoice;

//         DB::beginTransaction();
//         try {
//             // handle bukti baru jika ada (controller-level) — service boleh juga handle ini
//             if ($request->hasFile('bukti')) {
//                 // hapus lama jika ada di storage
//                 if ($payment->bukti_transfer && Storage::disk('public')->exists($payment->bukti_transfer)) {
//                     Storage::disk('public')->delete($payment->bukti_transfer);
//                 }

//                 $file = $request->file('bukti');
//                 $path = $file->store('payments', 'public');
//                 $payment->bukti_transfer = $path;
//             }

//             $payment->jumlah = $request->jumlah;
//             $payment->metode = $request->metode;
//             $payment->tanggal_bayar = Carbon::parse($request->tanggal_bayar)->startOfDay();
//             $payment->keterangan = $request->keterangan;
//             $payment->save();

//             // recalc totals via service (centralize logic)
//             if ($invoice) {
//                 $this->service->recalculateInvoiceTotals($invoice);
//             }

//             PaymentLogs::create([
//                 'payment_id' => $payment->id,
//                 'action'     => 'update',
//                 'meta'       => json_encode(['info' => 'Pembayaran diperbarui']),
//                 'created_by' => Auth::id(),
//             ]);

//             DB::commit();

//             return redirect()->route('keuangan.payments.show', $payment->id)
//                 ->with('success', 'Pembayaran berhasil diperbarui.');
//         } catch (\Throwable $e) {
//             DB::rollBack();
//             return back()->withInput()->with('error', 'Gagal update pembayaran: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Soft delete payment (delegasi ke service)
//      */
//     public function destroy($id)
//     {
//         $payment = Payments::findOrFail($id);

//         try {
//             $this->service->softDeletePayment($payment, Auth::id());
//             return redirect()->route('keuangan.payments.index')->with('success', 'Pembayaran berhasil dihapus.');
//         } catch (\Throwable $e) {
//             return back()->with('error', 'Gagal hapus: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Validate payment (admin action) — delegasi ke service
//      */
//     public function validatePayment(Request $request, $id)
//     {
//         $payment = Payments::with('invoice')->findOrFail($id);

//         try {
//             $this->service->validatePayment($payment, Auth::id());
//             return redirect()->route('keuangan.payments.show', $payment->id)->with('success', 'Pembayaran berhasil divalidasi.');
//         } catch (\Throwable $e) {
//             return back()->with('error', 'Gagal validasi: ' . $e->getMessage());
//         }
//     }

//     public function rejectPayment($id)
//     {
//         $p = Payments::findOrFail($id);

//         $p->status = 'ditolak';
//         $p->save();

//         if ($p->invoice) {
//             $p->invoice->updateSummary();
//         }

//         return back()->with('success', 'Pembayaran ditolak.');
//     }

//     /**
//      * AJAX: ambil invoice rekomendasi untuk jamaah
//      */
//     public function ajaxInvoice($jamaah_id)
//     {
//         // Ambil jamaah + invoice terakhir (belum lunas)
//         $invoice = Invoices::where('jamaah_id', $jamaah_id)
//             ->whereIn('status', ['belum_lunas', 'cicilan'])
//             ->orderBy('id', 'DESC')
//             ->first();

//         if ($invoice) {
//             return response()->json([
//                 'invoice' => [
//                     'id'             => $invoice->id,
//                     'total_tagihan'  => $invoice->total_tagihan,
//                     'total_terbayar' => $invoice->total_terbayar,
//                     'sisa_tagihan'   => $invoice->sisa_tagihan,
//                 ],
//                 'rekomendasi_total_tagihan' => null
//             ]);
//         }

//         // Jika belum punya invoice → ambil harga paket jamaah
//         $jamaah = Jamaah::find($jamaah_id);
//         $harga = $jamaah?->harga_akhir ?? 0;

//         return response()->json([
//             'invoice' => null,
//             'rekomendasi_total_tagihan' => $harga,
//         ]);
//     }


//     /**
//      * Print kwitansi simple view
//      */
//     public function printKwitansi($id)
//     {
//         $payment = Payments::with(['jamaah', 'invoice'])->findOrFail($id);
//         return view('keuangan.payments.kwitansi', compact('payment'));
//     }

//     /**
//      * Print premium kwitansi (web)
//      */
//     public function printKwitansiPremium($id)
//     {
//         $payment = Payments::with(['jamaah','invoice'])->findOrFail($id);

//         $invoice = $payment->invoice;
//         $jamaah  = $payment->jamaah;

//         // History pembayaran valid
//         $history = Payments::where('invoice_id', $invoice->id)
//             ->where(function ($q) {
//                 $q->whereNull('is_deleted')->orWhere('is_deleted', 0);
//             })
//             ->where('status', 'valid')
//             ->orderBy('tanggal_bayar','asc')
//             ->get();

//         // Perhitungan invoice dinamis
//         $invoice->total_terbayar = $history->sum('jumlah');
//         $invoice->sisa_tagihan   = max(0, $invoice->total_tagihan - $invoice->total_terbayar);

//         $invoice->status =
//             $invoice->sisa_tagihan <= 0 ? 'lunas' :
//             ($invoice->total_terbayar > 0 ? 'cicilan' : 'belum_lunas');

//         // ===================== QR CODE (SVG BASE64 SAFE) ===================== //
//         $qrRaw = \QrCode::format('svg')
//             ->size(120)
//             ->errorCorrection('H')
//             ->generate($invoice->nomor_invoice);

//         $qrCode = base64_encode($qrRaw);
//         // ===================================================================== //

//         $pdf = \PDF::loadView('keuangan.payments.print-premium', [
//             'payment' => $payment,
//             'invoice' => $invoice,
//             'jamaah'  => $jamaah,
//             'history' => $history,
//             'qrCode'  => $qrCode,
//         ])->setPaper('A4', 'portrait');

//         return $pdf->stream("kwitansi-{$payment->id}.pdf");
//     }



//     /**
//      * Export CSV (simple)
//      */
//     public function exportExcel(Request $request)
//     {
//         $payments = Payments::with(['jamaah','invoice'])->get();
//         $csv = "id,jamaah,invoice,jumlah,status,tanggal\n";
//         foreach ($payments as $p) {
//             $csv .= implode(',', [
//                 $p->id,
//                 '"'.($p->jamaah->nama_lengkap ?? '').'"',
//                 ($p->invoice->nomor_invoice ?? ''),
//                 $p->jumlah,
//                 $p->status,
//                 $p->tanggal_bayar,
//             ]) . "\n";
//         }
//         $name = 'payments-'.date('Ymd-His').'.csv';
//         return response($csv)
//             ->header('Content-Type', 'text/csv')
//             ->header('Content-Disposition', "attachment; filename={$name}");
//     }

//     /**
//      * Bin (soft-deleted list)
//      */
//     public function bin()
//     {
//         $payments = Payments::with(['jamaah','invoice'])
//             ->where('is_deleted', 1)
//             ->orderBy('edited_at', 'desc')
//             ->paginate(20);

//         return view('keuangan.payments.bin', compact('payments'));
//     }

//     /**
//      * Restore soft-deleted payment
//      */
//     public function restore($id)
//     {
//         $payment = Payments::findOrFail($id);
//         $invoice = $payment->invoice;

//         DB::beginTransaction();
//         try {
//             $payment->is_deleted = 0;
//             $payment->edited_by = Auth::id();
//             $payment->edited_at = Carbon::now();
//             $payment->save();

//             if ($invoice) {
//                 // gunakan service untuk rekalkulasi
//                 $this->service->recalculateInvoiceTotals($invoice);
//             }

//             PaymentLogs::create([
//                 'payment_id' => $payment->id,
//                 'action'     => 'restore',
//                 'meta'       => json_encode(['info' => 'Pembayaran direstore']),
//                 'created_by' => Auth::id(),
//             ]);

//             DB::commit();
//             return redirect()->route('keuangan.payments.bin')
//                 ->with('success', 'Pembayaran berhasil direstore.');
//         } catch (\Throwable $e) {
//             DB::rollBack();
//             return back()->with('error', 'Gagal restore: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Force delete (permanent)
//      */
//     public function forceDelete($id)
//     {
//         $payment = Payments::findOrFail($id);

//         DB::beginTransaction();
//         try {
//             // hapus fisik bukti
//             if ($payment->bukti_transfer && Storage::disk('public')->exists($payment->bukti_transfer)) {
//                 Storage::disk('public')->delete($payment->bukti_transfer);
//             }

//             // hapus logs
//             PaymentLogs::where('payment_id', $payment->id)->delete();

//             // hapus record
//             $payment->delete();

//             DB::commit();
//             return redirect()->route('keuangan.payments.bin')->with('success', 'Pembayaran dihapus permanen.');
//         } catch (\Throwable $e) {
//             DB::rollBack();
//             return back()->with('error', 'Gagal hapus permanen: ' . $e->getMessage());
//         }
//     }
// }

// namespace App\Http\Controllers\Keuangan;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Validator;
// use App\Models\Payments;
// use App\Models\Invoices;
// use App\Models\PaymentLogs;
// use App\Models\Jamaah;
// use Carbon\Carbon;

// class PaymentController extends Controller
// {
//     /**
//      * Index - list payments (compact / card view)
//      */
//    public function index(Request $request)
// {
//     $query = Payments::with(['jamaah', 'invoice'])
//         ->where(function($q){
//             $q->whereNull('is_deleted')
//               ->orWhere('is_deleted', 0);
//         })
//         ->orderBy('created_at', 'desc');

//     // filter keyword
//     if ($request->filled('q')) {
//         $q = $request->q;

//         $query->whereHas('jamaah', function ($j) use ($q) {
//             $j->where('nama_lengkap', 'like', "%$q%")
//               ->orWhere('no_id', 'like', "%$q%");
//         })
//         ->orWhere('jumlah', 'like', "%$q%");
//     }

//     // filter status
//     if ($request->filled('status')) {
//         $query->where('status', $request->status);
//     }

//     $payments = $query->paginate(20)->appends($request->query());

//     return view('keuangan.payments.index', compact('payments'));
// }

//     /**
//      * Create form
//      */
//   public function create(Request $request)
// {
//     $invoiceId = $request->invoice_id;

//     // ==========================
//     // MODE TAMBAH CICILAN
//     // ==========================
//     if ($invoiceId) {

//         $invoice = Invoices::with('jamaah')->findOrFail($invoiceId);

//         return view('keuangan.payments.create', [
//             'mode' => 'cicilan',
//             'invoice' => $invoice,
//             'jamaah' => $invoice->jamaah,
//             'total_tagihan' => $invoice->total_tagihan,
//             'total_terbayar' => $invoice->total_terbayar,
//             'sisa_tagihan' => $invoice->sisa_tagihan,
//         ]);
//     }

//     // ==========================
//     // MODE PEMBAYARAN BARU
//     // ==========================
//     $jamaahTanpaInvoice = Jamaah::whereDoesntHave('invoices')->get();

//     return view('keuangan.payments.create', [
//         'mode' => 'baru',
//         'jamaahTanpaInvoice' => $jamaahTanpaInvoice,
//     ]);
// }




//     /**
//      * Store payment (creates invoice if none)
//      */
//     public function store(Request $request)
//     {
//         $rules = [
//             'jamaah_id'    => 'required|integer|exists:jamaah,id',
//             'jumlah'       => 'required|numeric|min:1',
//             'tanggal_bayar' => 'required|date',
//             'metode'       => 'required|in:transfer,cash,kantor,gateway',
//             'bukti_transfer' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
//             'invoice_id'   => 'nullable|integer|exists:invoices,id',
//             'keterangan'   => 'nullable|string',
//         ];

//         $v = Validator::make($request->all(), $rules);
//         if ($v->fails()) {
//             return redirect()->back()->withErrors($v)->withInput();
//         }

//         DB::beginTransaction();
//         try {
//             $jamaah = Jamaah::findOrFail($request->jamaah_id);

//             // If invoice_id provided -> use it. If not, try find existing invoice for jamaah (open invoice),
//             // otherwise create new invoice automatically.
//             $invoice = null;
//             if ($request->filled('invoice_id')) {
//                 $invoice = Invoices::find($request->invoice_id);
//             } else {
//                 // prefer existing invoice not lunas
//                 $invoice = Invoices::where('jamaah_id', $jamaah->id)
//                     ->whereIn('status', ['belum_lunas','cicilan','menunggu_validasi'])
//                     ->orderBy('created_at', 'asc')
//                     ->first();
//             }

//             if (!$invoice) {
//                 // Create new invoice
//                 // Determine total_tagihan: prefer jamaah.harga_disepakati, else harga_default, else paket price fallback (0)
//                 $total_tagihan = (int) ($jamaah->harga_disepakati ?: $jamaah->harga_default ?: 0);

//                 // Generate nominal invoice number (safe simple)
//                 $nomor = $this->generateInvoiceNumber();

//                 $invoice = Invoices::create([
//                     'jamaah_id'      => $jamaah->id,
//                     'nomor_invoice'  => $nomor,
//                     'tanggal'        => Carbon::now()->toDateString(),
//                     'total_tagihan'  => $total_tagihan,
//                     'total_terbayar' => 0,
//                     'sisa_tagihan'   => $total_tagihan,
//                     'status'         => 'belum_lunas',
//                 ]);

//                 // log invoice creation (payment_logs table stores invoice creation logs with null payment_id)
//                 PaymentLogs::create([
//                     'payment_id' => null,
//                     'action'     => 'create',
//                     'meta'       => json_encode(['info' => 'Invoice otomatis dibuat', 'invoice' => $nomor]),
//                     'created_by' => Auth::id(),
//                 ]);
//             }

//             // Handle file upload
//             $buktiPath = null;
//             if ($request->hasFile('bukti_transfer')) {
//                 $file = $request->file('bukti_transfer');
//                 $path = $file->store('payments', 'public');
//                 $buktiPath = $path;
//             }

//             // Create payment record — default status pending; admin will validate later
//             $payment = Payments::create([
//                 'jamaah_id'     => $jamaah->id,
//                 'invoice_id'    => $invoice->id,
//                 'metode'        => $request->metode,
//                 'tanggal_bayar' => Carbon::parse($request->tanggal_bayar)->startOfDay(),
//                 'jumlah'        => (int) $request->jumlah,
//                 'keterangan'    => $request->keterangan,
//                 'bukti_transfer'=> $buktiPath,
//                 'status'        => 'pending', // user upload pending
//                 'created_by'    => Auth::id(),
//             ]);

//             // Log payment create
//             PaymentLogs::create([
//                 'payment_id' => $payment->id,
//                 'action'     => 'create',
//                 'meta'       => json_encode(['info' => 'Pembayaran baru dibuat']),
//                 'created_by' => Auth::id(),
//             ]);

//             DB::commit();

//             return redirect()->route('keuangan.payments.show', $payment->id)
//                 ->with('success', 'Pembayaran disimpan dan menunggu validasi.');
//         } catch (\Throwable $e) {
//             DB::rollBack();
//             return redirect()->back()->with('error', 'Gagal menyimpan pembayaran: '.$e->getMessage())->withInput();
//         }
//     }

//     /**
//      * Show payment + invoice history (compact)
//      */
//    public function show($id)
// {
//     // Ambil pembayaran utama + relasi
//     $payment = Payments::with(['jamaah', 'invoice'])->findOrFail($id);

//     // Pastikan invoice ada
//     if (!$payment->invoice) {
//         return back()->with('error', 'Invoice tidak ditemukan untuk pembayaran ini.');
//     }

//     $invoice = $payment->invoice;
//     $jamaah  = $payment->jamaah;

//     /*
//     |--------------------------------------------------------------------------
//     | HISTORY PEMBAYARAN (VALID + TIDAK DELETED)
//     |--------------------------------------------------------------------------
//     | Catatan:
//     | Kita hanya menampilkan pembayaran yang:
//     | 1. invoice_id sama
//     | 2. status = valid
//     | 3. is_deleted = NULL atau 0
//     */
//     $history = Payments::where('invoice_id', $invoice->id)
//         ->where('status', 'valid')
//         ->where(function ($q) {
//             $q->whereNull('is_deleted')
//               ->orWhere('is_deleted', 0);
//         })
//         ->orderBy('tanggal_bayar', 'asc')
//         ->get();

//     /*
//     |--------------------------------------------------------------------------
//     | LOG PEMBAYARAN (berdasarkan payment_id spesifik)
//     |--------------------------------------------------------------------------
//     */
//     $logs = PaymentLogs::where('payment_id', $payment->id)
//         ->orderBy('created_at', 'desc')
//         ->get();

//     /*
//     |--------------------------------------------------------------------------
//     | HITUNG ULANG TOTAL REAL-TIME
//     |--------------------------------------------------------------------------
//     */
//     $total_terbayar = $history->sum('jumlah');
//     $sisa_tagihan   = max(0, $invoice->total_tagihan - $total_terbayar);

//     // Update status real-time hanya untuk tampilan
//     $invoice->total_terbayar = $total_terbayar;
//     $invoice->sisa_tagihan   = $sisa_tagihan;
//     $invoice->status         = $sisa_tagihan <= 0 ? 'lunas' : 'cicilan';

//     return view('keuangan.payments.show', compact(
//         'payment',
//         'invoice',
//         'jamaah',
//         'history',
//         'logs',
//         'total_terbayar',
//         'sisa_tagihan'
//     ));
// }

//     /**
//      * Edit form for payment (admin correction)
//      */
//     public function edit($id)
//     {
//         $payment = Payments::with(['jamaah', 'invoice'])->findOrFail($id);
//         return view('keuangan.payments.edit', compact('payment'));
//     }

//     /**
//      * Update payment (basic: only allowed for certain fields)
//      */
//     public function update(Request $request, $id)
// {
//     $payment = Payments::findOrFail($id);
//     $invoice = $payment->invoice;

//     $request->validate([
//         'jumlah'        => 'required|numeric|min:1000',
//         'metode'        => 'required|string',
//         'tanggal_bayar' => 'required|date',
//         'keterangan'    => 'nullable|string',
//         'bukti'         => 'nullable|mimes:jpg,jpeg,png,pdf|max:4096',
//     ]);

//     DB::beginTransaction();

//     try {

//         // Hapus bukti lama jika upload baru
//         if ($request->hasFile('bukti')) {
//             if ($payment->bukti_transfer && Storage::exists($payment->bukti_transfer)) {
//                 Storage::delete($payment->bukti_transfer);
//             }

//             $file = $request->file('bukti');
//             $path = $file->store('payments');
//             $payment->bukti_transfer = $path;
//         }

//         // Update pembayaran
//         $payment->jumlah        = $request->jumlah;
//         $payment->metode        = $request->metode;
//         $payment->tanggal_bayar = $request->tanggal_bayar;
//         $payment->keterangan    = $request->keterangan;
//         $payment->save();

//         /*
//         |--------------------------------------------------------------------------
//         | HITUNG ULANG TOTAL PEMBAYARAN INVOICE
//         |--------------------------------------------------------------------------
//         */

//         $totalTerbayar = Payments::where('invoice_id', $invoice->id)
//             ->sum('jumlah');

//         $invoice->total_terbayar = $totalTerbayar;
//         $invoice->sisa_tagihan   = $invoice->total_tagihan - $totalTerbayar;

//         // Jika sudah lunas ubah status invoice
//         if ($invoice->sisa_tagihan <= 0) {
//             $invoice->status = 'lunas';
//             $invoice->sisa_tagihan = 0;
//         }

//         $invoice->save();

//         /*
//         |--------------------------------------------------------------------------
//         | LOG AKTIVITAS
//         |--------------------------------------------------------------------------
//         */
//         PaymentLogs::create([
//             'payment_id'  => $payment->id,
//             'action'      => 'update',
//             'meta'        => json_encode([
//                 'info'    => 'Pembayaran diperbarui',
//                 'jumlah'  => $payment->jumlah,
//                 'invoice' => $invoice->nomor_invoice
//             ]),
//             'created_by' => Auth::id(),
//         ]);

//         DB::commit();

//         return redirect()
//             ->route('keuangan.payments.show', $payment->id)
//             ->with('success', 'Pembayaran berhasil diperbarui.');

//     } catch (\Throwable $e) {

//         DB::rollBack();

//         return back()->with('error', 'Gagal update pembayaran: ' . $e->getMessage());
//     }
// }

//     /**
//      * Destroy payment (soft delete flag or delete)
//      */
//     public function destroy($id)
//     {
//         $payment = Payments::findOrFail($id);
//         $invoice = $payment->invoice;

//         DB::beginTransaction();
//         try {

//             // soft delete payment
//             $payment->is_deleted = 1;
//             $payment->edited_by = Auth::id();
//             $payment->edited_at = Carbon::now();
//             $payment->save();

//             // UPDATE ULANG INVOICE TOTAL
//             if ($invoice) {

//                 // hitung total terbayar hanya dari payment yang tidak terhapus
//                 $total = Payments::where('invoice_id', $invoice->id)
//                     ->where(function($q){
//                         $q->whereNull('is_deleted')
//                         ->orWhere('is_deleted', 0);
//                     })
//                     ->where('status', 'valid')
//                     ->sum('jumlah');

//                 $invoice->total_terbayar = $total;
//                 $invoice->sisa_tagihan   = max(0, $invoice->total_tagihan - $total);

//                 // update status
//                 if ($invoice->sisa_tagihan <= 0) {
//                     $invoice->status = 'lunas';
//                 } elseif ($total == 0) {
//                     $invoice->status = 'belum_lunas';
//                 } else {
//                     $invoice->status = 'cicilan';
//                 }

//                 $invoice->save();
//             }

//             // log delete
//             PaymentLogs::create([
//                 'payment_id' => $payment->id,
//                 'action'     => 'delete',
//                 'meta'       => json_encode(['info' => 'Pembayaran dihapus (soft)']),
//                 'created_by' => Auth::id(),
//             ]);

//             DB::commit();
//             return redirect()
//                 ->route('keuangan.payments.index')
//                 ->with('success', 'Pembayaran berhasil dihapus.');

//         } catch (\Throwable $e) {

//             DB::rollBack();
//             return redirect()->back()->with('error', 'Gagal hapus: '.$e->getMessage());
//         }
//     }


//     /**
//      * Validate payment (admin action) - update payment status to 'valid' and update invoice totals
//      */
//     public function validatePayment(Request $request, $id)
//     {
//         $payment = Payments::with('invoice')->findOrFail($id);

//         if ($payment->status === 'valid') {
//             return redirect()->back()->with('error', 'Pembayaran sudah divalidasi.');
//         }

//         DB::beginTransaction();
//         try {
//             // mark payment valid
//             $payment->status = 'valid';
//             $payment->validated_by = Auth::id();
//             $payment->validated_at = Carbon::now();
//             $payment->save();

//             // update invoice totals if invoice exists
//             if ($payment->invoice) {
//                 $invoice = $payment->invoice;
//                 $invoice->total_terbayar = ($invoice->payments()->where('status','valid')->sum('jumlah'));
//                 $invoice->sisa_tagihan = max(0, $invoice->total_tagihan - $invoice->total_terbayar);

//                 // set status
//                 $invoice->status = $invoice->sisa_tagihan <= 0 ? 'lunas' : 'cicilan';
//                 $invoice->save();
//             }

//             PaymentLogs::create([
//                 'payment_id' => $payment->id,
//                 'action'     => 'validate',
//                 'meta'       => json_encode(['info' => 'Pembayaran divalidasi']),
//                 'created_by' => Auth::id(),
//             ]);

//             DB::commit();
//             return redirect()->route('keuangan.payments.show', $payment->id)->with('success', 'Pembayaran berhasil divalidasi.');
//         } catch (\Throwable $e) {
//             DB::rollBack();
//             return redirect()->back()->with('error', 'Gagal validasi: '.$e->getMessage());
//         }
//     }

//     /**
//      * Correction endpoint: create correction record or mark is_correction
//      * This method just records a correction action and optionally create reversed entry - keep simple here
//      */
//     public function correction(Request $request, $id)
//     {
//         $payment = Payments::findOrFail($id);

//         $rules = [
//             'action' => 'required|in:correction',
//             'note'   => 'nullable|string',
//         ];
//         $v = Validator::make($request->all(), $rules);
//         if ($v->fails()) {
//             return redirect()->back()->withErrors($v);
//         }

//         DB::beginTransaction();
//         try {
//             // mark original as correction (soft flag)
//             $payment->is_correction = 1;
//             $payment->edited_by = Auth::id();
//             $payment->edited_at = Carbon::now();
//             $payment->save();

//             // log
//             PaymentLogs::create([
//                 'payment_id' => $payment->id,
//                 'action'     => 'correction',
//                 'meta'       => json_encode(['info' => 'Koreksi pembayaran', 'note' => $request->note]),
//                 'created_by' => Auth::id(),
//             ]);

//             DB::commit();
//             return redirect()->route('keuangan.payments.show', $payment->id)->with('success', 'Pembayaran dikoreksi.');
//         } catch (\Throwable $e) {
//             DB::rollBack();
//             return redirect()->back()->with('error', 'Gagal koreksi: '.$e->getMessage());
//         }
//     }

//     /**
//      * Print kwitansi (simple view)
//      */
//     public function printKwitansi($id)
//     {
//         $payment = Payments::with(['jamaah', 'invoice'])->findOrFail($id);
//         return view('keuangan.payments.kwitansi', compact('payment'));
//     }

//     /**
//      * Export Excel (placeholder)
//      */
//     public function exportExcel(Request $request)
//     {
//         // Implement with maatwebsite/excel or custom CSV. For now return simple download
//         $payments = Payments::with(['jamaah','invoice'])->get();
//         $csv = "id,jamaah,invoice,jumlah,status,tanggal\n";
//         foreach ($payments as $p) {
//             $csv .= implode(',', [
//                 $p->id,
//                 '"'.($p->jamaah->nama_lengkap ?? '').'"',
//                 ($p->invoice->nomor_invoice ?? ''),
//                 $p->jumlah,
//                 $p->status,
//                 $p->tanggal_bayar,
//             ]) . "\n";
//         }
//         $name = 'payments-'.date('Ymd-His').'.csv';
//         return response($csv)
//             ->header('Content-Type', 'text/csv')
//             ->header('Content-Disposition', "attachment; filename={$name}");
//     }

//     /**
//      * Export PDF (placeholder)
//      */
//     public function exportPdf(Request $request)
//     {
//         // Implement with dompdf or snappy. For now return redirect
//         return redirect()->back()->with('error', 'Fitur export PDF belum diimplementasikan. (Implement dengan dompdf)');
//     }

//     /**
//      * AJAX helper: return invoice info for a jamaah
//      * GET /keuangan/pembayaran/ajax-invoice/{jamaah_id}
//      */
//     public function ajaxInvoice($jamaah_id)
//     {
//         $jamaah = Jamaah::find($jamaah_id);
//         if (!$jamaah) {
//             return response()->json(['error' => 'Jamaah tidak ditemukan'], 404);
//         }

//         // cari invoice aktif (belum lunas/cicilan/menunggu_validasi)
//         $invoice = Invoices::where('jamaah_id', $jamaah->id)
//             ->whereIn('status', ['belum_lunas','cicilan','menunggu_validasi'])
//             ->orderBy('created_at','asc')
//             ->first();

//         // rekomendasi total tagihan dari jamaah (harga_disepakati atau harga_default)
//         $rekomendasi_total = (int) ($jamaah->harga_disepakati ?: $jamaah->harga_default ?: 0);

//         return response()->json([
//             'jamaah' => [
//                 'id' => $jamaah->id,
//                 'nama_lengkap' => $jamaah->nama_lengkap,
//                 'no_id' => $jamaah->no_id,
//             ],
//             'invoice' => $invoice ? [
//                 'id' => $invoice->id,
//                 'nomor_invoice' => $invoice->nomor_invoice,
//                 'total_tagihan' => (int)$invoice->total_tagihan,
//                 'total_terbayar' => (int)$invoice->total_terbayar,
//                 'sisa_tagihan' => (int)$invoice->sisa_tagihan,
//                 'status' => $invoice->status,
//             ] : null,
//             'nomor_invoice_baru' => $invoice ? null : $this->generateInvoiceNumber(),
//             'rekomendasi_total_tagihan' => $rekomendasi_total,
//         ]);
//     }

//     /**
//      * Helper - generate invoice number (simple incremental style)
//      */
//     protected function generateInvoiceNumber()
//     {
//         $prefix = 'INV-'.date('Y').'-';
//         // get last nomor_invoice with same year prefix
//         $last = Invoices::where('nomor_invoice', 'like', $prefix.'%')
//             ->orderBy('id','desc')->first();

//         if (!$last) {
//             return $prefix . str_pad(1, 5, '0', STR_PAD_LEFT);
//         }

//         // extract last numeric part
//         $parts = explode('-', $last->nomor_invoice);
//         $n = (int) end($parts);
//         return $prefix . str_pad($n + 1, 5, '0', STR_PAD_LEFT);
//     }

//     // Print PDF
//     /**
//  * Print Kwitansi Premium (Compact)
//  * URL: /keuangan/pembayaran/{id}/kwitansi-pdf
//  */

//     /**
//  * Print Kwitansi Premium (Web View)
//  * URL: /keuangan/pembayaran/{id}/kwitansi-premium
//  */
// public function printKwitansiPremium($id)
// {
//     $payment = Payments::with(['jamaah','invoice'])->findOrFail($id);

//     $invoice = $payment->invoice;
//     $jamaah  = $payment->jamaah;

//     // Ambil semua history valid
//     $history = Payments::where('invoice_id', $invoice->id)
//         ->where(function ($q) {
//             $q->whereNull('is_deleted')
//               ->orWhere('is_deleted', 0);
//         })
//         ->where('status', 'valid')
//         ->orderBy('tanggal_bayar','asc')
//         ->get();

//     $invoice->total_terbayar = $history->sum('jumlah');
//     $invoice->sisa_tagihan   = max(0, $invoice->total_tagihan - $invoice->total_terbayar);

//     if ($invoice->sisa_tagihan <= 0) {
//         $invoice->status = 'lunas';
//     } elseif ($invoice->total_terbayar > 0) {
//         $invoice->status = 'cicilan';
//     } else {
//         $invoice->status = 'belum_lunas';
//     }

//     return view('keuangan.payments.print-premium', compact(
//         'payment', 'invoice', 'jamaah', 'history'
//     ));
// }


//     public function bin()
//     {
//         $payments = Payments::with(['jamaah','invoice'])
//             ->where('is_deleted', 1)
//             ->orderBy('edited_at', 'desc')
//             ->paginate(20);

//         return view('keuangan.payments.bin', compact('payments'));
//     }

//     public function restore($id)
//     {
//         $payment = Payments::findOrFail($id);
//         $invoice = $payment->invoice;

//         DB::beginTransaction();
//         try {

//             // Restore payment
//             $payment->is_deleted = 0;
//             $payment->edited_by = Auth::id();
//             $payment->edited_at = Carbon::now();
//             $payment->save();

//             // Recalculate invoice
//             if ($invoice) {

//                 $total = Payments::where('invoice_id', $invoice->id)
//                     ->where('is_deleted', 0)        // FIX
//                     ->where('status', 'valid')
//                     ->sum('jumlah');

//                 $invoice->total_terbayar = $total;
//                 $invoice->sisa_tagihan = max(0, $invoice->total_tagihan - $total);

//                 $invoice->status = $invoice->sisa_tagihan <= 0 
//                     ? 'lunas' 
//                     : ($total > 0 ? 'cicilan' : 'belum_lunas');

//                 $invoice->save();
//             }

//             // Log restore
//             PaymentLogs::create([
//                 'payment_id' => $payment->id,
//                 'action' => 'restore',
//                 'meta' => json_encode(['info'=>'Pembayaran direstore']),
//                 'created_by' => Auth::id(),
//             ]);

//             DB::commit();
//             return redirect()->route('keuangan.payments.bin')
//                 ->with('success', 'Pembayaran berhasil direstore.');

//         } catch (\Throwable $e) {
//             DB::rollBack();
//             return back()->with('error', 'Gagal restore: '.$e->getMessage());
//         }
//     }

//     public function forceDelete($id)
//     {
//         $payment = Payments::findOrFail($id);

//         DB::beginTransaction();
//         try {

//             // Hapus bukti transfer fisik
//             if ($payment->bukti_transfer && \Storage::exists($payment->bukti_transfer)) {
//                 \Storage::delete($payment->bukti_transfer);
//             }

//             // Hapus logs
//             PaymentLogs::where('payment_id', $payment->id)->delete();

//             // Hapus data payment
//             $payment->delete();

//             DB::commit();
//             return redirect()->route('keuangan.payments.bin')
//                 ->with('success', 'Pembayaran dihapus permanen.');

//         } catch (\Throwable $e) {

//             DB::rollBack();
//             return back()->with('error', 'Gagal hapus permanen: '.$e->getMessage());
//         }
//     }



// }
