<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BeritaController extends Controller
{
    /**
     * INDEX + FILTER + SEARCH + PAGINATION
     */
    public function index(Request $r)
    {
        $q        = $r->q;
        $kategori = $r->kategori;

        $kategoriList = Berita::select('kategori')
            ->whereNotNull('kategori')
            ->distinct()
            ->pluck('kategori');

        $berita = Berita::when($q, function ($s) use ($q) {
                $s->where('judul', 'like', "%$q%")
                  ->orWhere('kategori', 'like', "%$q%");
            })
            ->when($kategori, function($s) use ($kategori) {
                $s->where('kategori', $kategori);
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(10)
            ->withQueryString();

        return view('admin.berita.index', [
            'berita'        => $berita,
            'kategoriList'  => $kategoriList,
            'q'             => $q,
        ]);
    }


    /**
     * SHOW CREATE FORM
     */
    public function create()
    {
        return view('admin.berita.create');
    }


    /**
     * STORE NEW BERITA
     */
    public function store(Request $req)
    {
        $req->validate([
            'judul'     => 'required|max:255',
            'kategori'  => 'nullable|max:100',
            'konten'    => 'nullable',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        // Upload thumbnail
        $thumb = null;
        if ($req->hasFile('thumbnail')) {
            $thumb = $req->file('thumbnail')->store('berita', 'public');
        }

        // Save
        Berita::create([
            'judul'     => $req->judul,
            'slug'      => Str::slug($req->judul) . '-' . Str::random(5),
            'kategori'  => $req->kategori,
            'konten'    => $req->konten,
            'thumbnail' => $thumb,
        ]);

        return redirect()->route('admin.berita.index')
                         ->with('success', 'Berita berhasil ditambahkan!');
    }


    /**
     * SHOW EDIT FORM
     */
    public function edit($id)
    {
        $item = Berita::findOrFail($id);
        return view('admin.berita.edit', compact('item'));
    }


    /**
     * UPDATE BERITA
     */
    public function update(Request $r, $id)
    {
        $item = Berita::findOrFail($id);

        $r->validate([
            'judul'     => 'required|max:255',
            'kategori'  => 'nullable|max:100',
            'konten'    => 'nullable',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $thumbnail = $item->thumbnail;

        // Jika upload gambar baru
        if ($r->hasFile('thumbnail')) {

            // Hapus gambar lama
            if ($thumbnail && Storage::disk('public')->exists($thumbnail)) {
                Storage::disk('public')->delete($thumbnail);
            }

            $thumbnail = $r->file('thumbnail')->store('berita', 'public');
        }

        $item->update([
            'judul'     => $r->judul,
            'slug'      => Str::slug($r->judul) . '-' . Str::random(5),
            'kategori'  => $r->kategori,
            'konten'    => $r->konten,
            'thumbnail' => $thumbnail,
        ]);

        return redirect()->route('admin.berita.index')
                         ->with('success', 'Berita berhasil diperbarui!');
    }


    /**
     * DELETE BERITA
     */
    public function destroy($id)
    {
        $item = Berita::findOrFail($id);

        // Hapus thumbnail jika ada
        if ($item->thumbnail && Storage::disk('public')->exists($item->thumbnail)) {
            Storage::disk('public')->delete($item->thumbnail);
        }

        $item->delete();

        return redirect()->route('admin.berita.index')
                         ->with('success', 'Berita berhasil dihapus!');
    }
}
