<?php

namespace App\Http\Controllers\Visa;

use App\Http\Controllers\Controller;
use App\Models\VisaOrder;
use App\Models\VisaPayment;
use App\Services\Visa\VisaPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VisaPaymentController extends Controller
{
    public function __construct(
        protected VisaPaymentService $visaPaymentService
    ) {
    }

    public function index(Request $request): View
    {
        $filters = [
            'keyword' => $request->string('keyword')->toString(),
            'payment_status' => $request->string('payment_status')->toString(),
            'payment_method' => $request->string('payment_method')->toString(),
        ];

        $payments = $this->visaPaymentService->getAll($filters, (int) $request->input('per_page', 15));

        return view('visa.payments.index', [
            'pageTitle' => 'Pembayaran Visa',
            'payments' => $payments,
            'filters' => $filters,
        ]);
    }

    public function create(Request $request): View
    {
        $order = null;

        if ($request->filled('visa_order_id')) {
            $order = VisaOrder::query()->findOrFail($request->integer('visa_order_id'));
        }

        return view('visa.payments.create', [
            'pageTitle' => 'Tambah Pembayaran Visa',
            'payment' => new VisaPayment(),
            'order' => $order,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'visa_order_id' => ['required', 'exists:visa_orders,id'],
            'payment_method' => ['required', 'string', 'max:50'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_status' => ['nullable', 'string', 'max:50'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'account_name' => ['nullable', 'string', 'max:100'],
            'paid_at' => ['nullable', 'date'],
            'note' => ['nullable', 'string'],
        ]);

        $order = VisaOrder::query()->findOrFail($validated['visa_order_id']);

        $payment = $this->visaPaymentService->create($order, [
            'payment_method' => $validated['payment_method'],
            'amount' => $validated['amount'],
            'payment_status' => $validated['payment_status'] ?? 'pending',
            'reference_number' => $validated['reference_number'] ?? null,
            'bank_name' => $validated['bank_name'] ?? null,
            'account_name' => $validated['account_name'] ?? null,
            'paid_at' => $validated['paid_at'] ?? null,
            'note' => $validated['note'] ?? null,
            'confirmed_by' => null,
            'confirmed_at' => null,
        ]);

        return redirect()
            ->route('visa.orders.show', $payment->visa_order_id)
            ->with('success', 'Pembayaran berhasil ditambahkan.');
    }

    public function show(VisaPayment $visaPayment): View
    {
        $visaPayment->load(['order']);

        return view('visa.payments.show', [
            'pageTitle' => 'Detail Pembayaran Visa',
            'payment' => $visaPayment,
        ]);
    }

    public function edit(VisaPayment $visaPayment): View
    {
        $visaPayment->load(['order']);

        return view('visa.payments.edit', [
            'pageTitle' => 'Edit Pembayaran Visa',
            'payment' => $visaPayment,
        ]);
    }

    public function update(Request $request, VisaPayment $visaPayment): RedirectResponse
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'max:50'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_status' => ['required', 'string', 'max:50'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'account_name' => ['nullable', 'string', 'max:100'],
            'paid_at' => ['nullable', 'date'],
            'note' => ['nullable', 'string'],
        ]);

        $visaPayment->update([
            'payment_method' => $validated['payment_method'],
            'amount' => $validated['amount'],
            'payment_status' => $validated['payment_status'],
            'reference_number' => $validated['reference_number'] ?? null,
            'bank_name' => $validated['bank_name'] ?? null,
            'account_name' => $validated['account_name'] ?? null,
            'paid_at' => $validated['paid_at'] ?? null,
            'note' => $validated['note'] ?? null,
        ]);

        $visaPayment->order?->recalculatePayment();

        return redirect()
            ->route('visa.payments.show', $visaPayment)
            ->with('success', 'Pembayaran berhasil diperbarui.');
    }

    public function destroy(VisaPayment $visaPayment): RedirectResponse
    {
        $orderId = $visaPayment->visa_order_id;

        $this->visaPaymentService->delete($visaPayment);

        return redirect()
            ->route('visa.orders.show', $orderId)
            ->with('success', 'Pembayaran berhasil dihapus.');
    }

    public function markPaid(Request $request, VisaPayment $visaPayment): RedirectResponse
    {
        $validated = $request->validate([
            'reference_number' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string'],
        ]);

        $this->visaPaymentService->markAsPaid(
            $visaPayment,
            $validated['reference_number'] ?? null,
            auth()->id(),
            $validated['note'] ?? null
        );

        return back()->with('success', 'Pembayaran berhasil ditandai lunas.');
    }

    public function markFailed(Request $request, VisaPayment $visaPayment): RedirectResponse
    {
        $validated = $request->validate([
            'note' => ['nullable', 'string'],
        ]);

        $this->visaPaymentService->markAsFailed(
            $visaPayment,
            $validated['note'] ?? null
        );

        return back()->with('success', 'Pembayaran ditandai gagal.');
    }

    public function markRefunded(Request $request, VisaPayment $visaPayment): RedirectResponse
    {
        $validated = $request->validate([
            'note' => ['nullable', 'string'],
        ]);

        $this->visaPaymentService->markAsRefunded(
            $visaPayment,
            $validated['note'] ?? null
        );

        return back()->with('success', 'Pembayaran ditandai refund.');
    }
}