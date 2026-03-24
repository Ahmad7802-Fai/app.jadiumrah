<?php

namespace App\Http\Controllers\Visa;

use App\Http\Controllers\Controller;
use App\Models\VisaProduct;
use App\Services\Visa\VisaProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VisaProductController extends Controller
{
    public function __construct(
        protected VisaProductService $visaProductService
    ) {
    }

    public function index(Request $request): View
    {
        $filters = [
            'keyword' => $request->string('keyword')->toString(),
            'product_type' => $request->string('product_type')->toString(),
            'is_active' => $request->has('is_active')
                ? $request->input('is_active')
                : '',
        ];

        $products = $this->visaProductService->getAll($filters, (int) $request->input('per_page', 15));

        return view('visa.products.index', [
            'products' => $products,
            'filters' => $filters,
            'pageTitle' => 'Produk Visa',
        ]);
    }

    public function create(): View
    {
        return view('visa.products.create', [
            'pageTitle' => 'Tambah Produk Visa',
            'product' => new VisaProduct(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['nullable', 'string', 'max:50', 'unique:visa_products,code'],
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:180', 'unique:visa_products,slug'],
            'product_type' => ['required', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'processing_days' => ['nullable', 'integer', 'min:0'],
            'validity_days' => ['nullable', 'integer', 'min:0'],
            'stay_days' => ['nullable', 'integer', 'min:0'],
            'entry_type' => ['nullable', 'string', 'max:50'],
            'quota' => ['nullable', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'admin_fee' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['processing_days'] = $validated['processing_days'] ?? 0;
        $validated['validity_days'] = $validated['validity_days'] ?? 0;
        $validated['stay_days'] = $validated['stay_days'] ?? 0;
        $validated['quota'] = $validated['quota'] ?? 0;
        $validated['admin_fee'] = $validated['admin_fee'] ?? 0;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);

        $product = $this->visaProductService->create($validated);

        return redirect()
            ->route('visa.products.show', $product)
            ->with('success', 'Produk visa berhasil dibuat.');
    }

    public function show(VisaProduct $visaProduct): View
    {
        return view('visa.products.show', [
            'pageTitle' => 'Detail Produk Visa',
            'product' => $visaProduct,
        ]);
    }

    public function edit(VisaProduct $visaProduct): View
    {
        return view('visa.products.edit', [
            'pageTitle' => 'Edit Produk Visa',
            'product' => $visaProduct,
        ]);
    }

    public function update(Request $request, VisaProduct $visaProduct): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['nullable', 'string', 'max:50', 'unique:visa_products,code,' . $visaProduct->id],
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:180', 'unique:visa_products,slug,' . $visaProduct->id],
            'product_type' => ['required', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'processing_days' => ['nullable', 'integer', 'min:0'],
            'validity_days' => ['nullable', 'integer', 'min:0'],
            'stay_days' => ['nullable', 'integer', 'min:0'],
            'entry_type' => ['nullable', 'string', 'max:50'],
            'quota' => ['nullable', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'admin_fee' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['processing_days'] = $validated['processing_days'] ?? 0;
        $validated['validity_days'] = $validated['validity_days'] ?? 0;
        $validated['stay_days'] = $validated['stay_days'] ?? 0;
        $validated['quota'] = $validated['quota'] ?? 0;
        $validated['admin_fee'] = $validated['admin_fee'] ?? 0;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        $this->visaProductService->update($visaProduct, $validated);

        return redirect()
            ->route('visa.products.show', $visaProduct)
            ->with('success', 'Produk visa berhasil diperbarui.');
    }

    public function destroy(VisaProduct $visaProduct): RedirectResponse
    {
        $this->visaProductService->delete($visaProduct);

        return redirect()
            ->route('visa.products.index')
            ->with('success', 'Produk visa berhasil dihapus.');
    }

    public function toggleActive(VisaProduct $visaProduct): RedirectResponse
    {
        $this->visaProductService->toggleActive($visaProduct);

        return back()->with('success', 'Status produk visa berhasil diperbarui.');
    }
}