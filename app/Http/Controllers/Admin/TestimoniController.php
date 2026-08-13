<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimoni;
use Illuminate\Http\Request;

class TestimoniController extends Controller
{
    public function index()
    {
        $items = Testimoni::orderBy('created_at', 'DESC')->get();
        return view('admin.testimoni.index', compact('items'));
    }

    public function create()
    {
        $item = new Testimoni();
        return view('admin.testimoni.create', compact('item'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'   => 'required|string|max:100',
            'pesan'  => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'photo'  => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['nama', 'pesan', 'rating']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('testimoni', 'public');
        }

        Testimoni::create($data);

        return redirect()->route('admin.testimoni.index')
            ->with('success', 'Testimoni berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $item = Testimoni::findOrFail($id);
        return view('admin.testimoni.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = Testimoni::findOrFail($id);

        $request->validate([
            'nama'   => 'required|string|max:100',
            'pesan'  => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'photo'  => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['nama', 'pesan', 'rating']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('testimoni', 'public');
        }

        $item->update($data);

        return redirect()->route('admin.testimoni.index')
            ->with('success', 'Testimoni berhasil diperbarui');
    }

    public function destroy($id)
    {
        Testimoni::findOrFail($id)->delete();

        return redirect()->route('admin.testimoni.index')
            ->with('success', 'Testimoni berhasil dihapus');
    }
}
