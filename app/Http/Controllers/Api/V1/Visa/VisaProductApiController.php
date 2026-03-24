<?php

namespace App\Http\Controllers\Api\V1\Visa;

use App\Http\Controllers\Controller;
use App\Models\VisaProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisaProductApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = VisaProduct::query();

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        $products = $query
            ->latest()
            ->paginate($request->integer('per_page', 10));

        return response()->json([
            'success' => true,
            'message' => 'Daftar produk visa berhasil diambil.',
            'data' => $products,
        ]);
    }

    public function show(VisaProduct $visaProduct): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail produk visa berhasil diambil.',
            'data' => $visaProduct,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code'             => ['required', 'string', 'max:50', 'unique:visa_products,code'],
            'name'             => ['required', 'string', 'max:255'],
            'country'          => ['required', 'string', 'max:100'],
            'visa_type'        => ['required', 'string', 'max:100'],
            'entry_type'       => ['nullable', 'string', 'max:50'],
            'processing_days'  => ['nullable', 'integer', 'min:0'],
            'validity_days'    => ['nullable', 'integer', 'min:0'],
            'price'            => ['required', 'numeric', 'min:0'],
            'description'      => ['nullable', 'string'],
            'requirements'     => ['nullable', 'string'],
            'is_active'        => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $validated['is_active'] ?? true;

        $product = VisaProduct::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Produk visa berhasil dibuat.',
            'data' => $product,
        ], 201);
    }

    public function update(Request $request, VisaProduct $visaProduct): JsonResponse
    {
        $validated = $request->validate([
            'code'             => ['required', 'string', 'max:50', 'unique:visa_products,code,' . $visaProduct->id],
            'name'             => ['required', 'string', 'max:255'],
            'country'          => ['required', 'string', 'max:100'],
            'visa_type'        => ['required', 'string', 'max:100'],
            'entry_type'       => ['nullable', 'string', 'max:50'],
            'processing_days'  => ['nullable', 'integer', 'min:0'],
            'validity_days'    => ['nullable', 'integer', 'min:0'],
            'price'            => ['required', 'numeric', 'min:0'],
            'description'      => ['nullable', 'string'],
            'requirements'     => ['nullable', 'string'],
            'is_active'        => ['nullable', 'boolean'],
        ]);

        $visaProduct->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Produk visa berhasil diperbarui.',
            'data' => $visaProduct->fresh(),
        ]);
    }

    public function destroy(VisaProduct $visaProduct): JsonResponse
    {
        $visaProduct->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk visa berhasil dihapus.',
        ]);
    }

    public function toggleActive(VisaProduct $visaProduct): JsonResponse
    {
        $visaProduct->update([
            'is_active' => ! $visaProduct->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status produk visa berhasil diperbarui.',
            'data' => $visaProduct->fresh(),
        ]);
    }
}