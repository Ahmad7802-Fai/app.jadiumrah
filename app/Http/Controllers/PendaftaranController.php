<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Jamaah\SelfRegistrationService;

class PendaftaranController extends Controller
{
    public function create()
    {
        return view('pendaftaran.create');
    }

    public function store(Request $request, SelfRegistrationService $service)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_hp'        => 'required|string|max:20',
            'id_paket'     => 'required|exists:paket_umrah,id',
            'tipe_jamaah'  => 'required|in:reguler,tabungan,cicilan',
            'email'        => 'nullable|email',
        ]);

        $service->register($request->all());

        return redirect()
            ->route('daftar.create')
            ->with('success', 'Pendaftaran berhasil. Tim kami akan menghubungi Anda.');
    }
}
