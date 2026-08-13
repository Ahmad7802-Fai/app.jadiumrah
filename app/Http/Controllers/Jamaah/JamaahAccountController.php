<?php

namespace App\Http\Controllers\Jamaah;

use App\Http\Controllers\Controller;
use App\Models\Jamaah;
use App\Services\Jamaah\JamaahAccountService;
use Illuminate\Http\Request;

class JamaahAccountController extends Controller
{
    public function __construct(
        protected JamaahAccountService $service
    ) {
        $this->middleware('permission:jamaah.account.view')->only('index');
        $this->middleware('permission:jamaah.account.create')->only(['create','bulkCreate']);
        $this->middleware('permission:jamaah.account.reset')->only('reset');
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX (List Account Jamaah)
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = Jamaah::with('user');

        // 🔎 Filter Status
        if ($request->status === 'no-account') {
            $query->whereDoesntHave('user');
        }

        if ($request->status === 'active') {
            $query->whereHas('user');
        }

        if ($request->status === 'inactive') {
            $query->whereHas('user', function ($q) {
                $q->where('is_active', false);
            });
        }

        $jamaahs = $query->latest()->paginate(15);

        return view('jamaahs.accounts.index', compact('jamaahs'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE ACCOUNT (Single)
    |--------------------------------------------------------------------------
    */
    public function create(Jamaah $jamaah)
    {
        if ($jamaah->user) {
            return back()->with('error', 'Jamaah sudah memiliki akun.');
        }

        $result = $this->service->createAccount($jamaah);

        return back()->with('success',
            'Akun berhasil dibuat. Password: '.$result['password']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | BULK CREATE ACCOUNT
    |--------------------------------------------------------------------------
    */
    public function bulkCreate()
    {
        $jamaahs = Jamaah::doesntHave('user')->get();

        foreach ($jamaahs as $jamaah) {
            $this->service->createAccount($jamaah);
        }

        return back()->with('success',
            'Semua akun jamaah berhasil dibuat.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RESET PASSWORD
    |--------------------------------------------------------------------------
    */
    public function reset(Jamaah $jamaah)
    {
        if (!$jamaah->user) {
            return back()->with('error', 'Jamaah belum memiliki akun.');
        }

        $password = $this->service->resetPassword($jamaah);

        return back()->with('success',
            'Password baru: '.$password
        );
    }

    public function deactivate(Jamaah $jamaah)
    {
        $this->service->deactivateAccount($jamaah);

        return back()->with('success','Akun berhasil dinonaktifkan.');
    }

    public function activate(Jamaah $jamaah)
    {
        $this->service->activateAccount($jamaah);

        return back()->with('success','Akun berhasil diaktifkan.');
    }

    public function sendWa(Jamaah $jamaah)
    {
        if (!$jamaah->user) {
            return back()->with('error','Jamaah belum punya akun.');
        }

        $this->service->sendPasswordViaWa($jamaah);

        return back()->with('success','Password berhasil dikirim via WhatsApp.');
    }

}