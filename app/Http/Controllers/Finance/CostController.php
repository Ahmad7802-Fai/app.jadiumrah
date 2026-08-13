<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Cost;
use App\Models\CostCategory;
use App\Models\Booking;
use App\Models\PaketDeparture;
use App\Services\Finance\CostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CostController extends Controller
{
    public function __construct(
        protected CostService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = Cost::with([
            'category',
            'branch',
            'booking',
            'departure',
            'creator',
            'approver'
        ])->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where('description','like','%'.$request->search.'%');
        }

        $costs = $query->paginate(15);

        return view('finance.costs.index', compact('costs'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $categories = CostCategory::where('is_active', true)->get();
        $bookings   = Booking::latest()->limit(50)->get();
        $departures = PaketDeparture::latest()->limit(50)->get();

        return view('finance.costs.create', compact(
            'categories',
            'bookings',
            'departures'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cost_category_id'   => 'required|exists:cost_categories,id',
            'booking_id'         => 'nullable|exists:bookings,id',
            'paket_departure_id' => 'nullable|exists:paket_departures,id',
            'amount'             => 'required|numeric|min:1',
            'description'        => 'nullable|string|max:500',
            'cost_date'          => 'required|date',
            'proof_file'         => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        /*
        |--------------------------------------------------------------------------
        | HANDLE FILE
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('proof_file')) {
            $validated['proof_file'] = $request
                ->file('proof_file')
                ->store('cost-proofs', 'public');
        }

        $this->service->create($validated);

        return redirect()
            ->route('finance.costs.index')
            ->with('success','Cost berhasil dibuat & menunggu approval.');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(Cost $cost)
    {
        $this->authorize('update', $cost);

        $categories = CostCategory::where('is_active', true)->get();
        $bookings   = Booking::latest()->limit(50)->get();
        $departures = PaketDeparture::latest()->limit(50)->get();

        return view('finance.costs.edit', compact(
            'cost',
            'categories',
            'bookings',
            'departures'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Cost $cost)
    {
        $this->authorize('update', $cost);

        $validated = $request->validate([
            'cost_category_id' => 'required|exists:cost_categories,id',
            'amount'           => 'required|numeric|min:1',
            'description'      => 'nullable|string|max:500',
            'cost_date'        => 'required|date',
            'proof_file'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        /*
        |--------------------------------------------------------------------------
        | HANDLE FILE REUPLOAD
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('proof_file')) {

            // hapus lama
            if ($cost->proof_file &&
                Storage::disk('public')->exists($cost->proof_file)) {

                Storage::disk('public')->delete($cost->proof_file);
            }

            $validated['proof_file'] = $request
                ->file('proof_file')
                ->store('cost-proofs', 'public');
        }

        $this->service->update($cost, $validated);

        return redirect()
            ->route('finance.costs.index')
            ->with('success','Cost berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE
    |--------------------------------------------------------------------------
    */
    public function approve(Cost $cost)
    {
        $this->authorize('approve', $cost);

        $this->service->approve($cost);

        return back()->with('success','Cost approved.');
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT
    |--------------------------------------------------------------------------
    */
    public function reject(Cost $cost)
    {
        $this->authorize('approve', $cost);

        $this->service->reject($cost);

        return back()->with('success','Cost rejected.');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy(Cost $cost)
    {
        $this->authorize('delete', $cost);

        $this->service->delete($cost);

        return back()->with('success','Cost dihapus.');
    }
}