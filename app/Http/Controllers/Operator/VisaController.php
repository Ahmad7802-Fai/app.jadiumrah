<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Visa;
use App\Models\Jamaah;
use App\Models\Keberangkatan;
use Illuminate\Http\Request;

class VisaController extends Controller
{
    public function index(Request $request)
    {
        $query = Visa::with(['jamaah', 'keberangkatan']);

        // Filter keberangkatan
        if ($request->keberangkatan_id) {
            $query->where('keberangkatan_id', $request->keberangkatan_id);
        }

        // Search nama jamaah atau nomor visa
        if ($request->search) {
            $s = $request->search;

            $query->whereHas('jamaah', function($q) use ($s) {
                $q->where('nama_lengkap', 'like', "%$s%");
            })
            ->orWhere('nomor_visa', 'like', "%$s%");
        }

        $visas = $query->latest()->paginate(20);
        $keberangkatan = Keberangkatan::orderBy('tanggal_berangkat')->get();

        return view('operator.visa.index', compact('visas','keberangkatan'));
    }


    public function create()
    {
        $jamaah = Jamaah::orderBy('nama_lengkap')->get();
        $keberangkatan = Keberangkatan::orderBy('tanggal_berangkat')->get();

        return view('operator.visa.create', compact('jamaah','keberangkatan'));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'jamaah_id' => 'required|exists:jamaah,id',
            'keberangkatan_id' => 'required|exists:keberangkatan,id',
            'status' => 'required|in:Proses,Approved,Rejected',
            'nomor_visa' => 'nullable|string|max:100',
        ]);

        Visa::create($data);

        return redirect()->route('operator.visa.index')
            ->with('success', 'Data visa berhasil ditambahkan.');
    }


    public function edit($id)
    {
        $visa = Visa::with(['jamaah', 'keberangkatan'])->findOrFail($id);
        $keberangkatan = Keberangkatan::orderBy('tanggal_berangkat')->get();

        return view('operator.visa.edit', compact('visa','keberangkatan'));
    }


    public function update(Request $request, $id)
    {
        $visa = Visa::findOrFail($id);

        $data = $request->validate([
            'status' => 'required|in:Proses,Approved,Rejected',
            'nomor_visa' => 'nullable|string|max:100',
            'keberangkatan_id' => 'required|exists:keberangkatan,id',
        ]);

        $visa->update($data);

        return redirect()->route('operator.visa.index')
            ->with('success', 'Data visa berhasil diperbarui.');
    }


    public function destroy($id)
    {
        Visa::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Data visa berhasil dihapus.');
    }
}
