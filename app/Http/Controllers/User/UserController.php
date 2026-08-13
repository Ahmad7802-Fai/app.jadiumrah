<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Services\Contracts\UserServiceInterface;

class UserController extends Controller
{
    protected UserServiceInterface $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->middleware('auth');

        $this->middleware('permission:user.view')->only('index', 'show');
        $this->middleware('permission:user.create')->only('create', 'store');
        $this->middleware('permission:user.update')->only('edit', 'update');
        $this->middleware('permission:user.delete')->only('destroy');

        $this->userService = $userService;
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $users = User::with(['roles', 'branch'])
            ->latest()
            ->paginate(20);

        return view('users.index', compact('users'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $roles = Role::all();
        $branches = Branch::where('is_active', true)->get();
        $permissions = Permission::orderBy('name')->get();

        return view('users.create', compact('roles', 'branches', 'permissions'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        $this->userService->create($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dibuat.');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(User $user)
    {
        $user->load(['roles', 'permissions', 'branch']);

        return view('users.show', compact('user'));
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(User $user)
    {
        $roles = Role::all();
        $branches = Branch::where('is_active', true)->get();
        $permissions = Permission::orderBy('name')->get();

        $userPermissions = $user
            ->getDirectPermissions()
            ->pluck('name')
            ->toArray();

        return view('users.edit', compact(
            'user',
            'roles',
            'branches',
            'permissions',
            'userPermissions'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, User $user)
    {
        $validated = $this->validateData($request, $user->id);

        $this->userService->update($user, $validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */

    public function destroy(User $user)
    {
        $this->userService->delete($user);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    private function validateData(Request $request, $ignoreId = null): array
    {
        return $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $ignoreId,
            'password'  => $ignoreId ? 'nullable|min:6' : 'required|min:6',
            'roles'     => 'required|array',
            'roles.*'   => 'exists:roles,name',

            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',

            'branch_id' => 'nullable|exists:branches,id',
        ]);
    }
}