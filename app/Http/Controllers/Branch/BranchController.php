<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Services\Branches\BranchService;

class BranchController extends Controller
{
    protected BranchService $branchService;

    public function __construct(BranchService $branchService)
    {
        $this->middleware('auth');

        $this->middleware('permission:branch.view')->only('index', 'show');
        $this->middleware('permission:branch.create')->only('create', 'store');
        $this->middleware('permission:branch.update')->only('edit', 'update');
        $this->middleware('permission:branch.delete')->only('destroy');

        $this->branchService = $branchService;
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $branches = Branch::latest()->paginate(20);

        return view('branches.index', compact('branches'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('branches.create');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'code'    => 'required|string|max:10|unique:branches,code',
            'city'    => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'phone'   => 'nullable|string|max:20',
        ]);

        $this->branchService->create($validated);

        return redirect()
            ->route('branches.index')
            ->with('success', 'Cabang berhasil dibuat.');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(Branch $branch)
    {
        return view('branches.show', compact('branch'));
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'city'    => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'phone'   => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $this->branchService->update($branch, $validated);

        return redirect()
            ->route('branches.index')
            ->with('success', 'Cabang berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */

    public function destroy(Branch $branch)
    {
        $this->branchService->delete($branch);

        return redirect()
            ->route('branches.index')
            ->with('success', 'Cabang berhasil dihapus.');
    }
}