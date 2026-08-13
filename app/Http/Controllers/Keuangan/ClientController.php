<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Tampilkan daftar seluruh client
     */
    public function index()
    {
        $clients = Client::orderBy('nama')->get();

        return view('keuangan.clients.index', compact('clients'));
    }

    /**
     * Form tambah client
     */
    public function create()
    {
        return view('keuangan.clients.create');
    }

    /**
     * Simpan client baru
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'tipe' => 'required|in:b2b,b2c',
            'nama' => 'required|string|max:255',
            'pic' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'npwp' => 'nullable|string|max:50',
        ]);

        Client::create($data);

        return redirect()
            ->route('keuangan.clients.index')
            ->with('success', 'Client berhasil ditambahkan.');
    }

    /**
     * Form edit client
     */
    public function edit($id)
    {
        $client = Client::findOrFail($id);

        return view('keuangan.clients.edit', compact('client'));
    }

    /**
     * Update data client
     */
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $data = $request->validate([
            'tipe' => 'required|in:b2b,b2c',
            'nama' => 'required|string|max:255',
            'pic' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'npwp' => 'nullable|string|max:50',
        ]);

        $client->update($data);

        return redirect()
            ->route('keuangan.clients.index')
            ->with('success', 'Client berhasil diperbarui.');
    }

    /**
     * Hapus client
     */
    public function destroy($id)
    {
        $client = Client::findOrFail($id);

        $client->delete();

        return redirect()
            ->route('keuangan.clients.index')
            ->with('success', 'Client berhasil dihapus.');
    }
}
