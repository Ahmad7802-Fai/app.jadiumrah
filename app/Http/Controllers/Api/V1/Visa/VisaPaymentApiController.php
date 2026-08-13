<?php

namespace App\Http\Controllers\Api\V1\Visa;

use App\Http\Controllers\Controller;
use App\Models\VisaPayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisaPaymentApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = VisaPayment::query()->with('order');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('visa_order_id')) {
            $query->where('visa_order_id', $request->visa_order_id);
        }

        $payments = $query->latest()->paginate($request->integer('per_page', 10));

        return response()->json([
            'success' => true,
            'message' => 'Daftar pembayaran visa berhasil diambil.',
            'data' => $payments,
        ]);
    }

    public function show(VisaPayment $visaPayment): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail pembayaran visa berhasil diambil.',
            'data' => $visaPayment->load('order'),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'visa_order_id'    => ['required', 'exists:visa_orders,id'],
            'amount'           => ['required', 'numeric', 'min:0'],
            'method'           => ['required', 'string', 'max:100'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'paid_at'          => ['nullable', 'date'],
            'status'           => ['nullable', 'string', 'max:50'],
            'notes'            => ['nullable', 'string'],
        ]);

        $payment = VisaPayment::create([
            ...$validated,
            'status' => $validated['status'] ?? 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran visa berhasil dibuat.',
            'data' => $payment->load('order'),
        ], 201);
    }

    public function update(Request $request, VisaPayment $visaPayment): JsonResponse
    {
        $validated = $request->validate([
            'amount'           => ['required', 'numeric', 'min:0'],
            'method'           => ['required', 'string', 'max:100'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'paid_at'          => ['nullable', 'date'],
            'status'           => ['nullable', 'string', 'max:50'],
            'notes'            => ['nullable', 'string'],
        ]);

        $visaPayment->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran visa berhasil diperbarui.',
            'data' => $visaPayment->fresh()->load('order'),
        ]);
    }

    public function destroy(VisaPayment $visaPayment): JsonResponse
    {
        $visaPayment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran visa berhasil dihapus.',
        ]);
    }

    public function markPaid(VisaPayment $visaPayment): JsonResponse
    {
        $visaPayment->update([
            'status'  => 'paid',
            'paid_at' => $visaPayment->paid_at ?? now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil ditandai lunas.',
            'data' => $visaPayment->fresh(),
        ]);
    }

    public function markFailed(VisaPayment $visaPayment): JsonResponse
    {
        $visaPayment->update([
            'status' => 'failed',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil ditandai gagal.',
            'data' => $visaPayment->fresh(),
        ]);
    }

    public function markRefunded(VisaPayment $visaPayment): JsonResponse
    {
        $visaPayment->update([
            'status' => 'refunded',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil ditandai refund.',
            'data' => $visaPayment->fresh(),
        ]);
    }
}