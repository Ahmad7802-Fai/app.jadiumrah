<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\Paket;
use Illuminate\Http\Request;

class FlashSaleController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = FlashSale::with('paket')->latest();

        if ($request->status === 'active') {
            $query->where('is_active', true);
        }

        if ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        $flashSales = $query->paginate(15);

        return view('marketing.flash_sales.index', compact('flashSales'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $pakets = Paket::where('is_active', true)->get();

        return view('marketing.flash_sales.create', compact('pakets'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'paket_id'      => 'required|exists:pakets,id',
            'discount_type' => 'required|in:fixed,percent',
            'value'         => 'required|numeric|min:0',
            'start_at'      => 'required|date',
            'end_at'        => 'required|date|after:start_at',
            'seat_limit'    => 'nullable|integer|min:1',
        ]);

        FlashSale::create($validated);

        return redirect()
            ->route('marketing.flash-sales.index')
            ->with('success', 'Flash Sale berhasil dibuat.');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(FlashSale $flashSale)
    {
        $pakets = Paket::where('is_active', true)->get();

        return view('marketing.flash_sales.edit', compact('flashSale','pakets'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, FlashSale $flashSale)
    {
        $validated = $request->validate([
            'paket_id'      => 'required|exists:pakets,id',
            'discount_type' => 'required|in:fixed,percent',
            'value'         => 'required|numeric|min:0',
            'start_at'      => 'required|date',
            'end_at'        => 'required|date|after:start_at',
            'seat_limit'    => 'nullable|integer|min:1',
            'is_active'     => 'boolean'
        ]);

        $flashSale->update($validated);

        return redirect()
            ->route('marketing.flash-sales.index')
            ->with('success', 'Flash Sale diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy(FlashSale $flashSale)
    {
        $flashSale->delete();

        return back()->with('success', 'Flash Sale dihapus.');
    }
}