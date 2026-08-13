<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $items = Gallery::orderBy('created_at', 'DESC')->get();
        return view('admin.gallery.index', compact('items'));
    }

    public function create()
    {
        return view('admin.gallery.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|max:150',
            'category' => 'nullable|max:100',
            'photo'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $photo = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->store('gallery', 'public');
        }

        Gallery::create([
            'title'    => $request->title,
            'category' => $request->category,
            'photo'    => $photo,
        ]);

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Foto galeri berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $item = Gallery::findOrFail($id);

        return view('admin.gallery.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = Gallery::findOrFail($id);

        $request->validate([
            'title' => 'required',
            'category' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['title', 'category']);

        // Upload file
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('gallery', 'public');
        }

        $item->update($data);

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Foto gallery berhasil diperbarui');
    }


    public function destroy(Gallery $gallery)
    {
        if ($gallery->photo && Storage::disk('public')->exists($gallery->photo)) {
            Storage::disk('public')->delete($gallery->photo);
        }

        $gallery->delete();

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Galeri berhasil dihapus!');
    }
}
