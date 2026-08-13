<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OperationalExpense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Exports\OperasionalExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class OperationalExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q       = $request->q;
        $tanggal = $request->tanggal;

        // FILTER BULAN & TAHUN
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        // ========== FILTER DATA LIST TABEL ----------
        $data = OperationalExpense::when($q, function ($query) use ($q) {
                    $query->where('kategori', 'like', "%$q%")
                        ->orWhere('deskripsi', 'like', "%$q%")
                        ->orWhere('jumlah', 'like', "%$q%");
                })
                ->when($tanggal, fn($query) => 
                    $query->whereDate('tanggal', $tanggal)
                )
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->orderBy('tanggal', 'desc')
                ->paginate(15);


        // ========== DASHBOARD REKAP ==========
        // Total pengeluaran bulan ini (berdasarkan filter)
        $totalBulan = OperationalExpense::whereYear('tanggal', $tahun)
                        ->whereMonth('tanggal', $bulan)
                        ->sum('jumlah');

        // Total pengeluaran tahun ini
        $totalTahun = OperationalExpense::whereYear('tanggal', $tahun)
                        ->sum('jumlah');

        // Pengeluaran per kategori bulan ini
        $kategoriBulan = OperationalExpense::selectRaw('kategori, SUM(jumlah) as total')
                            ->whereYear('tanggal', $tahun)
                            ->whereMonth('tanggal', $bulan)
                            ->groupBy('kategori')
                            ->get();

        // 5 pengeluaran terbesar bulan ini
        $top5 = OperationalExpense::whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->orderBy('jumlah', 'desc')
                ->take(5)
                ->get();

        return view('keuangan.biaya-operasional.index', compact(
            'data',
            'totalBulan',
            'totalTahun',
            'kategoriBulan',
            'top5',
            'bulan',
            'tahun'
        ));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('keuangan.biaya-operasional.create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kategori'   => 'required|string|max:100',
            'deskripsi'  => 'nullable|string',
            'jumlah'     => 'required|numeric|min:0',
            'tanggal'    => 'required|date',
            'bukti'      => 'nullable|file|max:4096|mimes:jpg,jpeg,png,pdf',
        ]);

        $fileName = null;

        if ($request->hasFile('bukti')) {
            $fileName = $request->file('bukti')->store('bukti-operasional', 'public');
        }

        OperationalExpense::create([
            'kategori'    => $request->kategori,
            'deskripsi'   => $request->deskripsi,
            'jumlah'      => $request->jumlah,
            'tanggal'     => $request->tanggal,
            'bukti'       => $fileName,
            'dibuat_oleh' => Auth::id(),
        ]);

        return redirect()->route('keuangan.operasional.index')
                         ->with('success', 'Biaya operasional berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $item = OperationalExpense::findOrFail($id);
        return view('keuangan.biaya-operasional.show', compact('item'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $item = OperationalExpense::findOrFail($id);
        return view('keuangan.biaya-operasional.edit', compact('item'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $item = OperationalExpense::findOrFail($id);

        $request->validate([
            'kategori'   => 'required|string|max:100',
            'deskripsi'  => 'nullable|string',
            'jumlah'     => 'required|numeric|min:0',
            'tanggal'    => 'required|date',
            'bukti'      => 'nullable|file|max:4096|mimes:jpg,jpeg,png,pdf',
        ]);

        $fileName = $item->bukti;

        if ($request->hasFile('bukti')) {
            if ($item->bukti && Storage::disk('public')->exists($item->bukti)) {
                Storage::disk('public')->delete($item->bukti);
            }

            $fileName = $request->file('bukti')->store('bukti-operasional', 'public');
        }

        $item->update([
            'kategori'   => $request->kategori,
            'deskripsi'  => $request->deskripsi,
            'jumlah'     => $request->jumlah,
            'tanggal'    => $request->tanggal,
            'bukti'      => $fileName,
        ]);

        return redirect()->route('keuangan.operasional.index')
                         ->with('success', 'Data biaya operasional berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = OperationalExpense::findOrFail($id);

        if ($item->bukti && Storage::disk('public')->exists($item->bukti)) {
            Storage::disk('public')->delete($item->bukti);
        }

        $item->delete();

        return redirect()->route('keuangan.operasional.index')
                         ->with('success', 'Data biaya operasional berhasil dihapus.');
    }

    public function exportExcel(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        return Excel::download(new OperasionalExport($bulan, $tahun), "operasional-$bulan-$tahun.xlsx");
    }

    public function exportPdf(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $data = OperationalExpense::whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->orderBy('tanggal', 'asc')
                    ->get();

        $total = $data->sum('jumlah');
        $namaBulan = \Carbon\Carbon::create()->month($bulan)->format('F');

        $pdf = PDF::loadView('keuangan.biaya-operasional.pdf', [
            'data' => $data,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namaBulan' => $namaBulan,
            'total' => $total
        ])->setPaper('A4', 'portrait');

        return $pdf->download("Laporan-Biaya-Operasional-$namaBulan-$tahun.pdf");
    }

}
