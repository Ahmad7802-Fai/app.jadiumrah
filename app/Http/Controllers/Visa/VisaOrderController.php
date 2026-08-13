<?php

namespace App\Http\Controllers\Visa;

use App\Http\Controllers\Controller;
use App\Models\VisaOrder;
use App\Models\VisaOrderTraveler;
use App\Services\Visa\VisaOrderService;
use App\Services\Visa\VisaProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VisaOrderController extends Controller
{
    public function __construct(
        protected VisaOrderService $visaOrderService,
        protected VisaProductService $visaProductService
    ) {
    }

    public function index(Request $request): View
    {
        $filters = [
            'keyword' => $request->string('keyword')->toString(),
            'order_status' => $request->string('order_status')->toString(),
            'payment_status' => $request->string('payment_status')->toString(),
            'departure_date' => $request->string('departure_date')->toString(),
        ];

        $orders = $this->visaOrderService->getAll($filters, (int) $request->input('per_page', 15));
        $summary = $this->visaOrderService->getDashboardSummary();

        return view('visa.orders.index', [
            'pageTitle' => 'Order Visa',
            'orders' => $orders,
            'summary' => $summary,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        $products = $this->visaProductService->getActiveProducts();

        return view('visa.orders.create', [
            'pageTitle' => 'Tambah Order Visa',
            'products' => $products,
            'order' => new VisaOrder(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'visa_product_id' => ['required', 'exists:visa_products,id'],

            'customer_name' => ['required', 'string', 'max:150'],
            'customer_email' => ['nullable', 'email', 'max:150'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'customer_address' => ['nullable', 'string'],

            'departure_date' => ['nullable', 'date'],
            'return_date' => ['nullable', 'date'],
            'departure_city' => ['nullable', 'string', 'max:100'],
            'destination_city' => ['nullable', 'string', 'max:100'],

            'order_status' => ['nullable', 'string', 'max:50'],
            'payment_status' => ['nullable', 'string', 'max:50'],

            'subtotal' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'admin_fee' => ['nullable', 'numeric', 'min:0'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'remaining_amount' => ['nullable', 'numeric', 'min:0'],

            'customer_note' => ['nullable', 'string'],
            'admin_note' => ['nullable', 'string'],

            'travelers' => ['nullable', 'array'],
            'travelers.*.full_name' => ['required_with:travelers', 'string', 'max:150'],
            'travelers.*.gender' => ['nullable', 'string', 'max:20'],
            'travelers.*.relationship' => ['nullable', 'string', 'max:50'],
            'travelers.*.is_main_applicant' => ['nullable', 'boolean'],
            'travelers.*.place_of_birth' => ['nullable', 'string', 'max:100'],
            'travelers.*.date_of_birth' => ['nullable', 'date'],
            'travelers.*.nationality' => ['nullable', 'string', 'max:100'],
            'travelers.*.nik' => ['nullable', 'string', 'max:50'],
            'travelers.*.passport_number' => ['nullable', 'string', 'max:50'],
            'travelers.*.passport_issue_date' => ['nullable', 'date'],
            'travelers.*.passport_expiry_date' => ['nullable', 'date'],
            'travelers.*.passport_issue_place' => ['nullable', 'string', 'max:100'],
            'travelers.*.address' => ['nullable', 'string'],
            'travelers.*.phone' => ['nullable', 'string', 'max:30'],
            'travelers.*.email' => ['nullable', 'email', 'max:150'],
            'travelers.*.father_name' => ['nullable', 'string', 'max:150'],
            'travelers.*.mother_name' => ['nullable', 'string', 'max:150'],
            'travelers.*.notes' => ['nullable', 'string'],
        ]);

        $validated['subtotal'] = $validated['subtotal'] ?? 0;
        $validated['discount_amount'] = $validated['discount_amount'] ?? 0;
        $validated['admin_fee'] = $validated['admin_fee'] ?? 0;
        $validated['total_amount'] = $validated['total_amount'] ?? 0;
        $validated['amount_paid'] = $validated['amount_paid'] ?? 0;
        $validated['remaining_amount'] = $validated['remaining_amount'] ?? 0;
        $validated['created_by'] = auth()->id();

        $order = $this->visaOrderService->create($validated);

        return redirect()
            ->route('visa.orders.show', $order)
            ->with('success', 'Order visa berhasil dibuat.');
    }

    public function show(VisaOrder $visaOrder): View
    {
        $order = $this->visaOrderService->getById($visaOrder->id);

        return view('visa.orders.show', [
            'pageTitle' => 'Detail Order Visa',
            'order' => $order,
        ]);
    }

    public function edit(VisaOrder $visaOrder): View
    {
        $order = $this->visaOrderService->getById($visaOrder->id);
        $products = $this->visaProductService->getActiveProducts();

        return view('visa.orders.edit', [
            'pageTitle' => 'Edit Order Visa',
            'order' => $order,
            'products' => $products,
        ]);
    }

    public function update(Request $request, VisaOrder $visaOrder): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'visa_product_id' => ['nullable', 'exists:visa_products,id'],

            'customer_name' => ['required', 'string', 'max:150'],
            'customer_email' => ['nullable', 'email', 'max:150'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'customer_address' => ['nullable', 'string'],

            'departure_date' => ['nullable', 'date'],
            'return_date' => ['nullable', 'date'],
            'departure_city' => ['nullable', 'string', 'max:100'],
            'destination_city' => ['nullable', 'string', 'max:100'],

            'subtotal' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'admin_fee' => ['nullable', 'numeric', 'min:0'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],

            'customer_note' => ['nullable', 'string'],
            'admin_note' => ['nullable', 'string'],
        ]);

        $validated['subtotal'] = $validated['subtotal'] ?? $visaOrder->subtotal;
        $validated['discount_amount'] = $validated['discount_amount'] ?? $visaOrder->discount_amount;
        $validated['admin_fee'] = $validated['admin_fee'] ?? $visaOrder->admin_fee;
        $validated['total_amount'] = $validated['total_amount'] ?? $visaOrder->total_amount;

        $this->visaOrderService->update($visaOrder, $validated);

        return redirect()
            ->route('visa.orders.show', $visaOrder)
            ->with('success', 'Order visa berhasil diperbarui.');
    }

    public function destroy(VisaOrder $visaOrder): RedirectResponse
    {
        $this->visaOrderService->delete($visaOrder);

        return redirect()
            ->route('visa.orders.index')
            ->with('success', 'Order visa berhasil dihapus.');
    }

    public function updateStatus(Request $request, VisaOrder $visaOrder): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
        ]);

        $this->visaOrderService->updateOrderStatus(
            $visaOrder,
            $validated['status'],
            $validated['description'] ?? null,
            auth()->id()
        );

        return back()->with('success', 'Status order berhasil diperbarui.');
    }

    public function addNote(Request $request, VisaOrder $visaOrder): RedirectResponse
    {
        $validated = $request->validate([
            'note' => ['required', 'string'],
            'type' => ['nullable', 'string', 'max:50'],
        ]);

        $this->visaOrderService->addNote(
            $visaOrder,
            $validated['note'],
            $validated['type'] ?? 'internal',
            auth()->id()
        );

        return back()->with('success', 'Catatan berhasil ditambahkan.');
    }

    public function addTraveler(Request $request, VisaOrder $visaOrder): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'gender' => ['nullable', 'string', 'max:20'],
            'relationship' => ['nullable', 'string', 'max:50'],
            'is_main_applicant' => ['nullable', 'boolean'],
            'place_of_birth' => ['nullable', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'nik' => ['nullable', 'string', 'max:50'],
            'passport_number' => ['nullable', 'string', 'max:50'],
            'passport_issue_date' => ['nullable', 'date'],
            'passport_expiry_date' => ['nullable', 'date'],
            'passport_issue_place' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'father_name' => ['nullable', 'string', 'max:150'],
            'mother_name' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->visaOrderService->addTraveler($visaOrder, $validated);

        return back()->with('success', 'Traveler berhasil ditambahkan.');
    }

    public function updateTraveler(Request $request, VisaOrder $visaOrder, VisaOrderTraveler $traveler): RedirectResponse
    {
        abort_unless((int) $traveler->visa_order_id === (int) $visaOrder->id, 404);

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'gender' => ['nullable', 'string', 'max:20'],
            'relationship' => ['nullable', 'string', 'max:50'],
            'is_main_applicant' => ['nullable', 'boolean'],
            'place_of_birth' => ['nullable', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'nik' => ['nullable', 'string', 'max:50'],
            'passport_number' => ['nullable', 'string', 'max:50'],
            'passport_issue_date' => ['nullable', 'date'],
            'passport_expiry_date' => ['nullable', 'date'],
            'passport_issue_place' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'father_name' => ['nullable', 'string', 'max:150'],
            'mother_name' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->visaOrderService->updateTraveler($traveler, $validated);

        return back()->with('success', 'Data traveler berhasil diperbarui.');
    }

    public function deleteTraveler(VisaOrder $visaOrder, VisaOrderTraveler $traveler): RedirectResponse
    {
        abort_unless((int) $traveler->visa_order_id === (int) $visaOrder->id, 404);

        $this->visaOrderService->deleteTraveler($traveler);

        return back()->with('success', 'Traveler berhasil dihapus.');
    }
}