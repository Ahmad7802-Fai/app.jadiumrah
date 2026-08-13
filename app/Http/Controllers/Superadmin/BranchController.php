<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Services\BranchService;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function __construct(
        protected BranchService $service
    ) {}

    public function index(Request $request)
    {
        $branches = Branch::withCount('agents')
            ->when($request->q, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('nama_cabang', 'like', "%{$request->q}%")
                        ->orWhere('kode_cabang', 'like', "%{$request->q}%")
                        ->orWhere('kota', 'like', "%{$request->q}%");
                });
            })
            ->when($request->status !== null && $request->status !== '', function ($q) use ($request) {
                $q->where('is_active', $request->status);
            })
            ->orderBy('nama_cabang')
            ->paginate(10);

        return view('superadmin.branch.index', compact('branches'));
    }


    public function create()
    {
        $this->authorize('create', Branch::class);

        return view('superadmin.branch.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Branch::class);

        $data = $request->validate([
            'prefix'         => 'required|string|max:5',
            'nama_cabang'    => 'required|string|max:100',
            'alamat'         => 'nullable|string',
            'kota'           => 'nullable|string',
            'admin_email'    => 'required|email|unique:users,email',
            'admin_password' => 'required|min:6',
        ]);

        try {
            $this->service->create($data);

            return redirect()
                ->route('superadmin.branch.index')
                ->with('success', 'Cabang & Admin berhasil dibuat');

        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }


    public function edit(Branch $branch)
    {
        $this->authorize('update', $branch);

        return view('superadmin.branch.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $this->authorize('update', $branch);

        $data = $request->validate([
            'kode_cabang' => 'required|string|max:20',
            'nama_cabang' => 'required|string|max:100',
            'alamat'      => 'nullable|string',
            'kota'        => 'nullable|string',
        ]);

        $this->service->update($branch->id, $data);

        return redirect()
            ->route('superadmin.branch.index')
            ->with('success', 'Cabang berhasil diperbarui.');
    }

    public function toggle(Branch $branch)
    {
        $this->authorize('toggle', $branch);

        $this->service->toggle($branch->id);

        return back()->with('success', 'Status cabang diperbarui.');
    }

    public function destroy(Branch $branch)
    {
        $this->authorize('delete', $branch);

        $this->service->delete($branch->id);

        return back()->with('success', 'Cabang berhasil dihapus.');
    }
}
