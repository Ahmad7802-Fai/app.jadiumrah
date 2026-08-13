<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Jamaah\SelfRegistrationService;

class PendaftaranController extends Controller
{
    public function __construct(
        protected SelfRegistrationService $service
    ) {}

    /**
     * ===============================
     * FORM PENDAFTARAN
     * ===============================
     */
    public function create(Request $request)
    {
        $referral = session('referral');

        // ❌ Harus dari link agen
        if (!$referral) {
            abort(403, 'Akses pendaftaran harus melalui link resmi agen');
        }

        // 🔒 Sudah submit → langsung ke sukses
        if (session()->has('registration_lock')) {
            return redirect()->route('website.daftar.success');
        }

        // 🧠 Simpan halaman asal (paket + agent)
        if ($request->headers->has('referer')) {
            session([
                'registration_from' => $request->headers->get('referer'),
            ]);
        }

        return view('website.daftar.create', compact('referral'));
    }

    /**
     * ===============================
     * SUBMIT SELF REGISTRATION
     * ===============================
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|min:3',
            'no_hp'        => 'required|string|min:8',
        ]);

        $this->service->register([
            'nama_lengkap' => $request->nama_lengkap,
            'no_hp'        => $request->no_hp,
            'email'        => $request->email,
            'kota'         => $request->kota,
            'jumlah'       => $request->jumlah,
            'catatan'      => $request->catatan,
        ]);

        // 🔒 Lock agar tidak submit ulang
        session(['registration_lock' => true]);

        return redirect()->route('website.daftar.success');
    }

    /**
     * ===============================
     * HALAMAN SUKSES
     * ===============================
     */
    public function success()
    {
        $referral = session('referral');

        if (!$referral) {
            abort(403);
        }

        return view('website.daftar.success', [
            'referral' => $referral,
            'from'     => session('registration_from'),
        ]);
    }
}
