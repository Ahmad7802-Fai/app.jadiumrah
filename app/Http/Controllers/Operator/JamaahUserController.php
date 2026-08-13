<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\Jamaah;
use App\Models\JamaahUser;
use App\Models\SecurityLog;
use App\Models\TabunganUmrah;

class JamaahUserController extends Controller
{
    /* ======================================================
     | LIST USER JAMAAH
     ====================================================== */
    public function index()
    {
        $users = JamaahUser::with('jamaah')
            ->latest()
            ->paginate(20);

        return view('operator.jamaah-user.index', compact('users'));
    }

    /* ======================================================
     | FORM CREATE USER JAMAAH
     ====================================================== */
    public function create()
    {
        // Jamaah yang BELUM punya akun login
        $jamaahList = Jamaah::whereDoesntHave('jamaahUser')
            ->orderBy('nama_lengkap')
            ->get();

        return view('operator.jamaah-user.create', compact('jamaahList'));
    }

    /* ======================================================
     | STORE USER JAMAAH
     ====================================================== */
    public function store(Request $request)
    {
        $request->validate([
            'jamaah_id' => 'required|exists:jamaah,id',
            'email'     => 'nullable|email|unique:jamaah_users,email',
            'phone'     => 'nullable|string|max:20|unique:jamaah_users,phone',
        ]);

        if (!$request->email && !$request->phone) {
            return back()->withErrors([
                'email' => 'Email atau No HP wajib diisi salah satu.',
            ])->withInput();
        }

        // 🔐 Generate password awal
        $plainPassword = Str::random(8);

        $user = JamaahUser::create([
            'jamaah_id'           => $request->jamaah_id,
            'email'               => $request->email,
            'phone'               => $request->phone,
            'password'            => Hash::make($plainPassword),
            'is_active'           => true,
            'password_changed_at' => now(),
        ]);

        /**
         * ✅ AUTO CREATE TABUNGAN UMRAH
         * (INI YANG SEBELUMNYA HILANG)
         */
        $jamaah = Jamaah::find($request->jamaah_id);

        TabunganUmrah::firstOrCreate(
            ['jamaah_id' => $jamaah->id],
            [
                'nomor_tabungan' => 'TAB-' . str_pad($jamaah->id, 4, '0', STR_PAD_LEFT),
                'nama_tabungan'  => 'Tabungan Umrah ' . $jamaah->nama_lengkap,
                'target_nominal' => 0,
                'saldo'          => 0,
                'status'         => 'ACTIVE',
            ]
        );

        // 🔐 Security log
        SecurityLog::create([
            'jamaah_user_id' => $user->id,
            'action'         => 'CREATE_USER',
            'description'    => 'Operator membuat akun jamaah',
            'ip_address'     => request()->ip(),
            'user_agent'     => substr(request()->userAgent(), 0, 500),
        ]);

        return redirect()
            ->route('operator.jamaah-user.index')
            ->with('success', "Akun jamaah berhasil dibuat. Password awal: {$plainPassword}");
    }

    /* ======================================================
     | RESET PASSWORD JAMAAH
     ====================================================== */
    public function resetPassword($id)
    {
        $user = JamaahUser::with('jamaah')->findOrFail($id);

        $newPassword = Str::random(10);

        $user->update([
            'password'            => Hash::make($newPassword),
            'password_changed_at' => now(),
        ]);

        SecurityLog::create([
            'jamaah_user_id' => $user->id,
            'action'         => 'RESET_PASSWORD',
            'description'    => 'Operator reset password jamaah: ' . ($user->jamaah->nama_lengkap ?? '-'),
            'ip_address'     => request()->ip(),
            'user_agent'     => substr(request()->userAgent(), 0, 500),
        ]);

        // FORCE LOGOUT handled by middleware password.fresh

        return back()->with([
            'success'       => 'Password berhasil direset.',
            'new_password'  => $newPassword, // tampil SEKALI
        ]);
    }

    /* ======================================================
     | TOGGLE AKTIF / NONAKTIF
     ====================================================== */
    public function toggleActive($id)
    {
        $user = JamaahUser::findOrFail($id);

        $newStatus = !$user->is_active;

        $user->update([
            'is_active' => $newStatus,
        ]);

        SecurityLog::create([
            'jamaah_user_id' => $user->id,
            'action'         => 'TOGGLE_ACTIVE',
            'description'    => 'Status akun diubah menjadi ' . ($newStatus ? 'AKTIF' : 'NONAKTIF'),
            'ip_address'     => request()->ip(),
            'user_agent'     => substr(request()->userAgent(), 0, 500),
        ]);

        return back()->with(
            'success',
            'Status akun berhasil diubah menjadi ' . ($newStatus ? 'AKTIF' : 'NONAKTIF')
        );
    }
}
