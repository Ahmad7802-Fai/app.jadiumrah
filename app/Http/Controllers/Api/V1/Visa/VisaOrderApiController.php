<?php

namespace App\Http\Controllers\Api\V1\Visa;

use App\Http\Controllers\Controller;
use App\Models\VisaOrder;
use App\Models\VisaOrderTraveler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisaOrderApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = VisaOrder::query()
            ->with(['product', 'travelers', 'payments']);

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('visa_product_id')) {
            $query->where('visa_product_id', $request->visa_product_id);
        }

        $orders = $query->latest()->paginate($request->integer('per_page', 10));

        return response()->json([
            'success' => true,
            'message' => 'Daftar order visa berhasil diambil.',
            'data' => $orders,
        ]);
    }

    public function show(VisaOrder $visaOrder): JsonResponse
    {
        $visaOrder->load([
            'product',
            'travelers',
            'documents',
            'payments',
            'statusHistories',
            'notes.user',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Detail order visa berhasil diambil.',
            'data' => $visaOrder,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'visa_product_id' => ['required', 'exists:visa_products,id'],
            'customer_name'   => ['required', 'string', 'max:255'],
            'customer_phone'  => ['required', 'string', 'max:50'],
            'customer_email'  => ['nullable', 'email', 'max:255'],
            'notes'           => ['nullable', 'string'],
            'total_amount'    => ['nullable', 'numeric', 'min:0'],
            'status'          => ['nullable', 'string', 'max:50'],
        ]);

        $order = DB::transaction(function () use ($validated) {
            $order = VisaOrder::create([
                ...$validated,
                'order_number' => 'VISA-' . now()->format('YmdHis'),
                'status'       => $validated['status'] ?? 'draft',
            ]);

            $order->statusHistories()->create([
                'status'     => $order->status,
                'notes'      => 'Order dibuat',
                'changed_by' => auth()->id(),
            ]);

            return $order;
        });

        return response()->json([
            'success' => true,
            'message' => 'Order visa berhasil dibuat.',
            'data' => $order->load('product'),
        ], 201);
    }

    public function update(Request $request, VisaOrder $visaOrder): JsonResponse
    {
        $validated = $request->validate([
            'visa_product_id' => ['required', 'exists:visa_products,id'],
            'customer_name'   => ['required', 'string', 'max:255'],
            'customer_phone'  => ['required', 'string', 'max:50'],
            'customer_email'  => ['nullable', 'email', 'max:255'],
            'notes'           => ['nullable', 'string'],
            'total_amount'    => ['nullable', 'numeric', 'min:0'],
        ]);

        $visaOrder->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Order visa berhasil diperbarui.',
            'data' => $visaOrder->fresh()->load('product'),
        ]);
    }

    public function destroy(VisaOrder $visaOrder): JsonResponse
    {
        $visaOrder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order visa berhasil dihapus.',
        ]);
    }

    public function updateStatus(Request $request, VisaOrder $visaOrder): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'max:50'],
            'notes'  => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated, $visaOrder) {
            $visaOrder->update([
                'status' => $validated['status'],
            ]);

            $visaOrder->statusHistories()->create([
                'status'     => $validated['status'],
                'notes'      => $validated['notes'] ?? null,
                'changed_by' => auth()->id(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Status order visa berhasil diperbarui.',
            'data' => $visaOrder->fresh(),
        ]);
    }

    public function addNote(Request $request, VisaOrder $visaOrder): JsonResponse
    {
        $validated = $request->validate([
            'note' => ['required', 'string'],
        ]);

        $note = $visaOrder->notes()->create([
            'note'     => $validated['note'],
            'user_id'  => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Catatan order berhasil ditambahkan.',
            'data' => $note,
        ], 201);
    }

    public function addTraveler(Request $request, VisaOrder $visaOrder): JsonResponse
    {
        $validated = $request->validate([
            'full_name'         => ['required', 'string', 'max:255'],
            'passport_number'   => ['required', 'string', 'max:100'],
            'nationality'       => ['nullable', 'string', 'max:100'],
            'date_of_birth'     => ['nullable', 'date'],
            'gender'            => ['nullable', 'string', 'max:20'],
        ]);

        $traveler = $visaOrder->travelers()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Traveler berhasil ditambahkan.',
            'data' => $traveler,
        ], 201);
    }

    public function updateTraveler(Request $request, VisaOrder $visaOrder, VisaOrderTraveler $traveler): JsonResponse
    {
        abort_if($traveler->visa_order_id !== $visaOrder->id, 404);

        $validated = $request->validate([
            'full_name'         => ['required', 'string', 'max:255'],
            'passport_number'   => ['required', 'string', 'max:100'],
            'nationality'       => ['nullable', 'string', 'max:100'],
            'date_of_birth'     => ['nullable', 'date'],
            'gender'            => ['nullable', 'string', 'max:20'],
        ]);

        $traveler->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Traveler berhasil diperbarui.',
            'data' => $traveler->fresh(),
        ]);
    }

    public function deleteTraveler(VisaOrder $visaOrder, VisaOrderTraveler $traveler): JsonResponse
    {
        abort_if($traveler->visa_order_id !== $visaOrder->id, 404);

        $traveler->delete();

        return response()->json([
            'success' => true,
            'message' => 'Traveler berhasil dihapus.',
        ]);
    }
}