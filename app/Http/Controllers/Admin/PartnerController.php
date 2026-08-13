<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::latest()->get();
        return view('admin.partner.index', compact('partners'));
    }

    public function create()
    {
        return view('admin.partner.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|max:255',
            'logo' => 'nullable|image|max:2048',
            'website' => 'nullable|url',
        ]);

        $data = $request->only(['nama', 'website']);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('partners', 'public');
        }

        Partner::create($data);

        return redirect()->route('admin.partner.index')
            ->with('success', 'Partner berhasil ditambahkan.');
    }

    public function edit(Partner $partner)
    {
        return view('admin.partner.edit', compact('partner'));
    }

    public function update(Request $request, Partner $partner)
    {
        $request->validate([
            'nama' => 'required|max:255',
            'logo' => 'nullable|image|max:2048',
            'website' => 'nullable|url',
        ]);

        $data = $request->only(['nama', 'website']);

        if ($request->hasFile('logo')) {

            if ($partner->logo && file_exists(storage_path('app/public/' . $partner->logo))) {
                unlink(storage_path('app/public/' . $partner->logo));
            }

            $data['logo'] = $request->file('logo')->store('partners', 'public');
        }

        $partner->update($data);

        return redirect()->route('admin.partner.index')
            ->with('success', 'Partner berhasil diupdate.');
    }

    public function destroy(Partner $partner)
    {
        if ($partner->logo && file_exists(storage_path('app/public/' . $partner->logo))) {
            unlink(storage_path('app/public/' . $partner->logo));
        }

        $partner->delete();

        return redirect()->route('admin.partner.index')
            ->with('success', 'Partner berhasil dihapus.');
    }
}
