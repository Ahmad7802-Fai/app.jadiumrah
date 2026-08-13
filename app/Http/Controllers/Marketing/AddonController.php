<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\MarketingAddon;
use App\Services\Marketing\AddonService;
use Illuminate\Http\Request;

class AddonController extends Controller
{
    public function __construct(
        protected AddonService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = MarketingAddon::query()->latest();

        if ($request->search) {
            $query->where('name','like','%'.$request->search.'%');
        }

        if ($request->status !== null) {
            $query->where('is_active', $request->status);
        }

        $addons = $query->paginate(15);

        return view('marketing.addons.index', compact('addons'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        return view('marketing.addons.create');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'code'          => 'required|string|max:50|unique:marketing_addons,code',
            'description'   => 'nullable|string',
            'selling_price' => 'required|numeric|min:0',
            'cost_price'    => 'nullable|numeric|min:0',
            'is_active'     => 'nullable|boolean',
        ]);

        $this->service->create($validated);

        return redirect()
            ->route('marketing.addons.index')
            ->with('success','Add-On berhasil dibuat.');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(MarketingAddon $addon)
    {
        return view('marketing.addons.edit', compact('addon'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, MarketingAddon $addon)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'selling_price' => 'required|numeric|min:0',
            'cost_price'    => 'nullable|numeric|min:0',
            'is_active'     => 'nullable|boolean',
        ]);

        $this->service->update($addon, $validated);

        return redirect()
            ->route('marketing.addons.index')
            ->with('success','Add-On berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy(MarketingAddon $addon)
    {
        $this->service->delete($addon);

        return back()->with('success','Add-On dihapus.');
    }
}