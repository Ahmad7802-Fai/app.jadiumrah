<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Keberangkatan;
use App\Models\PaketMaster;
use Illuminate\Http\Request;

class KeberangkatanController extends Controller
{
    /**
     * INDEX — List keberangkatan
     */
    public function index(Request $r)
    {
        $q      = $r->q;
        $status = $r->status;

        $data = Keberangkatan::with('paketMaster')
            ->when($q, function ($s) use ($q) {
                $s->where('kode_keberangkatan', 'like', "%$q%")
                  ->orWhereHas('paketMaster', function ($pm) use ($q) {
                      $pm->where('nama_paket', 'like', "%$q%");
                  });
            })
            ->when($status, fn($s) => $s->where('status', $status))
            ->orderBy('tanggal_berangkat', 'DESC')
            ->paginate(12);

        return view('operator.keberangkatan.index', [
            'data' => $data,
            'q'    => $q,
            'status' => $status,
        ]);
    }

    /**
     * CREATE — Show form create
     */
    public function create()
    {
        $paket = PaketMaster::where('is_active', '1')->orderBy('nama_paket')->get();
        return view('operator.keberangkatan.create', compact('paket'));
    }

    /**
     * STORE — Save create data
     */
    public function store(Request $r)
    {
        $r->validate([
            'id_paket_master'   => 'required|exists:paket_master,id',
            'kode_keberangkatan' => 'required|max:50|unique:keberangkatan,kode_keberangkatan',
            'tanggal_berangkat'  => 'required|date',
            'tanggal_pulang'     => 'required|date|after_or_equal:tanggal_berangkat',
            'kuota'              => 'required|integer|min:1',
            'seat_terisi'        => 'nullable|integer|min:0',
            'jumlah_jamaah'      => 'nullable|integer|min:0',
            'status'             => 'required|in:Aktif,Selesai,Batal',
        ]);

        Keberangkatan::create([
            'id_paket_master'   => $r->id_paket_master,
            'kode_keberangkatan' => $r->kode_keberangkatan,
            'tanggal_berangkat'  => $r->tanggal_berangkat,
            'tanggal_pulang'     => $r->tanggal_pulang,
            'kuota'              => $r->kuota,
            'seat_terisi'        => $r->seat_terisi ?? 0,
            'jumlah_jamaah'      => $r->jumlah_jamaah ?? 0,
            'status'             => $r->status,
        ]);

        return redirect()
            ->route('operator.keberangkatan.index')
            ->with('success', 'Keberangkatan berhasil ditambahkan!');
    }

    /**
     * EDIT — Show edit form
     */
    public function edit($id)
    {
        $item  = Keberangkatan::findOrFail($id);
        $paket = PaketMaster::where('is_active', '1')->orderBy('nama_paket')->get();

        return view('operator.keberangkatan.edit', compact('item', 'paket'));
    }

    /**
     * UPDATE — Save changes
     */
    public function update(Request $r, $id)
    {
        $item = Keberangkatan::findOrFail($id);

        $r->validate([
            'id_paket_master'   => 'required|exists:paket_master,id',
            'kode_keberangkatan' => 'required|max:50|unique:keberangkatan,kode_keberangkatan,' . $id,
            'tanggal_berangkat'  => 'required|date',
            'tanggal_pulang'     => 'required|date|after_or_equal:tanggal_berangkat',
            'kuota'              => 'required|integer|min:1',
            'seat_terisi'        => 'nullable|integer|min:0',
            'jumlah_jamaah'      => 'nullable|integer|min:0',
            'status'             => 'required|in:Aktif,Selesai,Batal',
        ]);

        $item->update([
            'id_paket_master'   => $r->id_paket_master,
            'kode_keberangkatan' => $r->kode_keberangkatan,
            'tanggal_berangkat'  => $r->tanggal_berangkat,
            'tanggal_pulang'     => $r->tanggal_pulang,
            'kuota'              => $r->kuota,
            'seat_terisi'        => $r->seat_terisi ?? 0,
            'jumlah_jamaah'      => $r->jumlah_jamaah ?? 0,
            'status'             => $r->status,
        ]);

        return redirect()
            ->route('operator.keberangkatan.index')
            ->with('success', 'Keberangkatan berhasil diperbarui!');
    }

    /**
     * DELETE — Remove item
     */
    public function destroy($id)
    {
        $item = Keberangkatan::findOrFail($id);
        $item->delete();

        return redirect()
            ->route('operator.keberangkatan.index')
            ->with('success', 'Keberangkatan berhasil dihapus!');
    }
}
