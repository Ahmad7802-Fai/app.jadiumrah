<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->q, function ($q) use ($request) {
                $q->where('nama', 'like', '%'.$request->q.'%')
                ->orWhere('email', 'like', '%'.$request->q.'%')
                ->orWhere('username', 'like', '%'.$request->q.'%');
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.users.index', [
            'title' => 'Manajemen User',
            'users' => $users,
        ]);
    }


    public function create()
    {
        $roles = ['SUPERADMIN','ADMIN','OPERATOR','KEUANGAN','INVENTORY','SALES'];
        $user = new \App\Models\User();

        return view('superadmin.users.create', compact('roles','user'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'role'     => 'required|string',
            'password' => 'required|min:6',
        ]);

        User::create([
            'nama'     => $request->nama,
            'email'    => $request->email,
            'username' => explode('@', $request->email)[0], // AUTO
            'role'     => $request->role,
            'password' => bcrypt($request->password),
        ]);

        return redirect()
            ->route('superadmin.users.index')
            ->with('success', 'User berhasil dibuat.');
    }

    

    public function edit($id)
    {
        $user = User::findOrFail($id);

        $roles = ['SUPERADMIN','ADMIN','OPERATOR','KEUANGAN','INVENTORY','SALES'];

        return view('superadmin.users.edit', compact('user','roles'));
    }


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|string',
            'password' => 'nullable|min:6',
        ]);

        $user->nama  = $request->nama;
        $user->email = $request->email;
        $user->role  = $request->role;

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()
            ->route('superadmin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User dihapus');
    }
}
