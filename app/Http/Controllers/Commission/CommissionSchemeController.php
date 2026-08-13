<?php

namespace App\Http\Controllers\Commission;

use App\Http\Controllers\Controller;
use App\Models\CommissionScheme;
use App\Services\Commission\CommissionSchemeService;
use Illuminate\Http\Request;

class CommissionSchemeController extends Controller
{
    protected CommissionSchemeService $service;

    public function __construct(CommissionSchemeService $service)
    {
        $this->service = $service;

        $this->middleware('permission:commission.view')->only('index');
        $this->middleware('permission:commission.create')->only('create','store');
        $this->middleware('permission:commission.update')->only('edit','update');
        $this->middleware('permission:commission.delete')->only('destroy');
    }

    public function index()
    {
        $schemes = CommissionScheme::latest()->paginate(15);

        return view('commission.index', compact('schemes'));
    }

    public function create()
    {
        return view('commission.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'year'      => 'required|integer|min:2020',
            'is_active' => 'nullable|boolean',
        ]);

        $this->service->create($validated);

        return redirect()
            ->route('commission.schemes.index')
            ->with('success', 'Commission scheme created.');
    }

    public function edit(CommissionScheme $commission_scheme)
    {
        return view('commission.edit', compact('commission_scheme'));
    }

    public function update(Request $request, CommissionScheme $commission_scheme)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'year'      => 'required|integer|min:2020',
            'is_active' => 'nullable|boolean',
        ]);

        $this->service->update($commission_scheme, $validated);

        return redirect()
            ->route('commission.schemes.index')
            ->with('success', 'Commission scheme updated.');
    }

    public function destroy(CommissionScheme $commission_scheme)
    {
        $this->service->delete($commission_scheme);

        return redirect()
            ->route('commission.schemes.index')
            ->with('success', 'Commission scheme deleted.');
    }
}