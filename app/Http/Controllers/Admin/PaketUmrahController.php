<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaketUmrah;
use App\Services\Paket\PaketUmrahService;
use App\Services\Paket\PaketUmrahUpdateService;
use App\Services\Paket\PaketUmrahDeleteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaketUmrahController extends Controller
{
    /**
     * ===============================
     * LIST
     * ===============================
     */
    public function index(Request $req)
    {
        $q      = $req->q;
        $status = $req->status;
        $durasi = $req->durasi;

        $durasiList = PaketUmrah::select('durasi')->distinct()->pluck('durasi');

        $data = PaketUmrah::query()
            ->when($q, fn ($s) => $s->where('title', 'like', "%$q%"))
            ->when($status, fn ($s) => $s->where('status', $status))
            ->when($durasi, fn ($s) => $s->where('durasi', $durasi))
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.paket-umrah.index', compact(
            'data',
            'q',
            'status',
            'durasi',
            'durasiList'
        ));
    }

    /**
     * ===============================
     * CREATE FORM
     * ===============================
     */
    public function create()
    {
        return view('admin.paket-umrah.create');
    }

    /**
     * ===============================
     * STORE (CREATE MASTER + KEBERANGKATAN + UMRAH)
     * ===============================
     */
    public function store(Request $req, PaketUmrahService $service)
    {
        $data = $req->validate([
            'title'         => 'required|string',
            'seo_title'     => 'nullable|string',
            'tglberangkat'  => 'required|date',
            'pesawat'       => 'required|string',
            'flight'        => 'required|string',
            'durasi'        => 'required|integer',
            'seat'          => 'required|integer',
            'hotmekkah'     => 'required|string',
            'rathotmekkah'  => 'required|integer',
            'hotmadinah'    => 'required|string',
            'rathotmadinah' => 'required|integer',
            'quad'          => 'required|integer',
            'triple'        => 'required|integer',
            'double'        => 'required|integer',
            'itin'          => 'required|string',
            'photo'         => 'nullable|image|max:2048',
            'thaif'         => 'required',
            'dubai'         => 'required',
            'kereta'        => 'required',
            'deskripsi'     => 'required|string',
            'allow_self_register' => 'nullable|boolean',
        ]);

        // upload photo
        if ($req->hasFile('photo')) {
            $data['photo'] = $req->file('photo')->store('paket', 'public');
        }

        $data['slug']      = Str::slug($data['title']) . '-' . time();
        $data['seo_title'] = $data['seo_title'] ?? $data['title'];

        $service->create($data);

        return redirect()
            ->route('admin.paket-umrah.index')
            ->with('success', 'Paket Umrah berhasil ditambahkan');
    }

    /**
     * ===============================
     * EDIT FORM
     * ===============================
     */
    public function edit($id)
    {
        $item = PaketUmrah::findOrFail($id);
        return view('admin.paket-umrah.edit', compact('item'));
    }

    /**
     * ===============================
     * UPDATE (SYNC ALL)
     * ===============================
     */
    public function update(
        Request $req,
        $id,
        PaketUmrahUpdateService $service
    ) {
        $paket = PaketUmrah::findOrFail($id);

        $data = $req->validate([
            'title'         => 'required|string',
            'seo_title'     => 'nullable|string',
            'tglberangkat'  => 'required|date',
            'pesawat'       => 'required|string',
            'flight'        => 'required|string',
            'durasi'        => 'required|integer',
            'seat'          => 'required|integer',
            'hotmekkah'     => 'required|string',
            'rathotmekkah'  => 'required|integer',
            'hotmadinah'    => 'required|string',
            'rathotmadinah' => 'required|integer',
            'quad'          => 'required|integer',
            'triple'        => 'required|integer',
            'double'        => 'required|integer',
            'itin'          => 'required|string',
            'photo'         => 'nullable|image|max:2048',
            'thaif'         => 'required',
            'dubai'         => 'required',
            'kereta'        => 'required',
            'deskripsi'     => 'required|string',
        ]);

        // photo handling
        if ($req->hasFile('photo')) {
            if ($paket->photo && Storage::disk('public')->exists($paket->photo)) {
                Storage::disk('public')->delete($paket->photo);
            }
            $data['photo'] = $req->file('photo')->store('paket', 'public');
        }

        $data['seo_title'] = $data['seo_title'] ?? $data['title'];

        $service->update($paket, $data);

        return redirect()
            ->route('admin.paket-umrah.index')
            ->with('success', 'Paket Umrah berhasil diperbarui');
    }

    /**
     * ===============================
     * DELETE (NON AKTIF / SAFE)
     * ===============================
     */
    public function destroy(
        $id,
        PaketUmrahDeleteService $service
    ) {
        $paket = PaketUmrah::findOrFail($id);

        // optional: hapus file foto
        if ($paket->photo && Storage::disk('public')->exists($paket->photo)) {
            Storage::disk('public')->delete($paket->photo);
        }

        $service->delete($paket);

        return redirect()
            ->route('admin.paket-umrah.index')
            ->with('success', 'Paket Umrah berhasil dinonaktifkan');
    }
}


// namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
// use App\Models\PaketUmrah;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Str;

// class PaketUmrahController extends Controller
// {
//     public function index(Request $req)
//     {
//         $q       = $req->q;
//         $status  = $req->status;
//         $durasi  = $req->durasi;

//         // List durasi unik untuk filter
//         $durasiList = PaketUmrah::select('durasi')->distinct()->pluck('durasi');

//         $data = PaketUmrah::when($q, function ($s) use ($q) {
//                     $s->where('title', 'like', "%$q%");
//                 })
//                 ->when($status, function ($s) use ($status) {
//                     $s->where('status', $status);
//                 })
//                 ->when($durasi, function ($s) use ($durasi) {
//                     $s->where('durasi', $durasi);
//                 })
//                 ->orderBy('created_at', 'DESC')
//                 ->paginate(9);

//         return view('admin.paket-umrah.index', compact('data', 'q', 'status', 'durasiList'));
//     }

//     public function create()
//     {
//         return view('admin.paket-umrah.create');
//     }

//     public function store(Request $req)
//     {
        
//         $req->validate([
//             'title'         => 'required',
//             'seo_title'     => 'required',
//             'tglberangkat'  => 'required|date',
//             'pesawat'       => 'required',
//             'flight'        => 'required',
//             'durasi'        => 'required|numeric',
//             'seat'          => 'required|numeric',
//             'hotmekkah'     => 'required',
//             'rathotmekkah'  => 'required|numeric',
//             'hotmadinah'    => 'required',
//             'rathotmadinah' => 'required|numeric',
//             'quad'          => 'required|numeric',
//             'triple'        => 'required|numeric',
//             'double'        => 'required|numeric',
//             'itin'          => 'required',
//             'photo'         => 'nullable|image|max:2048',
//             'thaif'         => 'required',
//             'dubai'         => 'required',
//             'kereta'        => 'required',
//             'deskripsi'     => 'required',
//             'status'        => 'required',
//         ]);

//         $slug = Str::slug($req->title) . '-' . time();

//         $photo = null;
//         if ($req->hasFile('photo')) {
//             $photo = $req->file('photo')->store('paket', 'public');
//         }

//         $seoTitle = $req->seo_title ?: $req->title;
//         PaketUmrah::create([
//             'title'         => $req->title,
//             'slug'          => $slug,
//             'seo_title' => $seoTitle,
//             'tglberangkat'  => $req->tglberangkat,
//             'pesawat'       => $req->pesawat,
//             'flight'        => $req->flight,
//             'durasi'        => $req->durasi,
//             'seat'          => $req->seat,
//             'hotmekkah'     => $req->hotmekkah,
//             'rathotmekkah'  => $req->rathotmekkah,
//             'hotmadinah'    => $req->hotmadinah,
//             'rathotmadinah' => $req->rathotmadinah,
//             'quad'          => $req->quad,
//             'triple'        => $req->triple,
//             'double'        => $req->double,
//             'itin'          => $req->itin,
//             'photo'         => $photo,
//             'thaif'         => $req->thaif,
//             'dubai'         => $req->dubai,
//             'kereta'        => $req->kereta,
//             'deskripsi'     => $req->deskripsi,
//             'status'        => $req->status,
//         ]);

//         return redirect()->route('admin.paket-umrah.index')->with('success', 'Paket Umrah berhasil ditambahkan!');
//     }


//     public function edit($id)
//     {
//         $item = PaketUmrah::findOrFail($id);
//         return view('admin.paket-umrah.edit', compact('item'));
//     }


//     public function update(Request $req, $id)
//     {
//         $item = PaketUmrah::findOrFail($id);

//         $req->validate([
//             'title'         => 'required',
//             'seo_title'     => 'required',
//             'tglberangkat'  => 'required|date',
//             'pesawat'       => 'required',
//             'flight'        => 'required',
//             'durasi'        => 'required|numeric',
//             'seat'          => 'required|numeric',
//             'hotmekkah'     => 'required',
//             'rathotmekkah'  => 'required|numeric',
//             'hotmadinah'    => 'required',
//             'rathotmadinah' => 'required|numeric',
//             'quad'          => 'required|numeric',
//             'triple'        => 'required|numeric',
//             'double'        => 'required|numeric',
//             'itin'          => 'required',
//             'photo'         => 'nullable|image|max:2048',
//             'thaif'         => 'required',
//             'dubai'         => 'required',
//             'kereta'        => 'required',
//             'deskripsi'     => 'required',
//             'status'        => 'required',
//         ]);

//         $photo = $item->photo;

//         if ($req->hasFile('photo')) {
//             if ($photo && Storage::disk('public')->exists($photo)) {
//                 Storage::disk('public')->delete($photo);
//             }
//             $photo = $req->file('photo')->store('paket', 'public');
//         }

//         $item->update([
//             'title'         => $req->title,
//             'slug'          => Str::slug($req->title) . '-' . $item->id,
//             'seo_title'     => $req->seo_title ?: $req->title,
//             'tglberangkat'  => $req->tglberangkat,
//             'pesawat'       => $req->pesawat,
//             'flight'        => $req->flight,
//             'durasi'        => $req->durasi,
//             'seat'          => $req->seat,
//             'hotmekkah'     => $req->hotmekkah,
//             'rathotmekkah'  => $req->rathotmekkah,
//             'hotmadinah'    => $req->hotmadinah,
//             'rathotmadinah' => $req->rathotmadinah,
//             'quad'          => $req->quad,
//             'triple'        => $req->triple,
//             'double'        => $req->double,
//             'itin'          => $req->itin,
//             'photo'         => $photo,
//             'thaif'         => $req->thaif,
//             'dubai'         => $req->dubai,
//             'kereta'        => $req->kereta,
//             'deskripsi'     => $req->deskripsi,
//             'status'        => $req->status,
//         ]);

//         return redirect()->route('admin.paket.index')->with('success', 'Paket Umrah berhasil diperbarui!');
//     }


//     public function destroy($id)
//     {
//         $item = PaketUmrah::findOrFail($id);

//         if ($item->photo && Storage::disk('public')->exists($item->photo)) {
//             Storage::disk('public')->delete($item->photo);
//         }

//         $item->delete();

//         return redirect()->route('admin.paket-umrah.index')->with('success', 'Paket Umrah berhasil dihapus!');
//     }
// }
