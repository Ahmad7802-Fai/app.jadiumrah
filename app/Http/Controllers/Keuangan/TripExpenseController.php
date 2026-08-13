<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Services\Keuangan\TripExpensesService;
use App\Models\PaketMaster;
use App\Models\Keberangkatan;
use App\Models\TripExpenses;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class TripExpenseController extends Controller
{
    public function __construct(
        protected TripExpensesService $service
    ) {}

    /* =====================================================
     | LIST BIAYA PER PAKET
     ===================================================== */
    public function index(int $paket_id)
    {
        $paket = PaketMaster::findOrFail($paket_id);

        return view('keuangan.trip-expenses.index', [
            'paket'            => $paket,
            'data'             => $this->service->listByPaket($paket_id),
            'totalPengeluaran' => $this->service->totalByPaket($paket_id),
            'totalJamaah'      => $this->service->totalJamaahByPaket($paket_id),
        ]);
    }

    /* =====================================================
     | FORM CREATE
     ===================================================== */
    public function create($paket_id)
    {
        $paket = PaketMaster::findOrFail($paket_id);

        $keberangkatanList = Keberangkatan::where('id_paket_master', $paket_id)
            ->orderByDesc('tanggal_berangkat')
            ->get();

        return view('keuangan.trip-expenses.create', compact(
            'paket',
            'keberangkatanList'
        ));
    }


    /* =====================================================
     | STORE
     ===================================================== */
    public function store(Request $request, int $paket_id)
    {
        $data = $request->validate([
            'keberangkatan_id' => 'required|exists:keberangkatan,id',
            'kategori'         => 'required|string|max:100',
            'jumlah'           => 'required|numeric|min:0',
            'tanggal'          => 'required|date',
            'catatan'          => 'nullable|string',
            'bukti'            => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        $this->service->create($paket_id, $data);

        return redirect()
            ->route('keuangan.trip.expenses.index', $paket_id)
            ->with('success', 'Biaya keberangkatan berhasil ditambahkan.');
    }

    /* =====================================================
     | EDIT
     ===================================================== */
    public function edit(int $paket_id, int $id)
    {
        $item = \App\Models\TripExpenses::where('paket_id', $paket_id)->findOrFail($id);

        return view('keuangan.trip-expenses.edit', [
            'paket' => PaketMaster::findOrFail($paket_id),
            'item'  => $item,
            'keberangkatanList' => Keberangkatan::where('id_paket_master', $paket_id)
                ->orderByDesc('tanggal_berangkat')
                ->get(),
        ]);
    }

    /* =====================================================
     | UPDATE
     ===================================================== */
    public function update(Request $request, int $paket_id, int $id)
    {
        $item = \App\Models\TripExpenses::where('paket_id', $paket_id)->findOrFail($id);

        $data = $request->validate([
            'keberangkatan_id' => 'required|exists:keberangkatan,id',
            'kategori'         => 'required|string|max:100',
            'jumlah'           => 'required|numeric|min:0',
            'tanggal'          => 'required|date',
            'catatan'          => 'nullable|string',
            'bukti'            => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        $item->update([
            'keberangkatan_id' => $data['keberangkatan_id'],
        ]);

        $this->service->update($item, $data);

        return redirect()
            ->route('keuangan.trip.expenses.index', $paket_id)
            ->with('success', 'Data berhasil diperbarui.');
    }

    /* =====================================================
     | DELETE
     ===================================================== */
    public function destroy(int $paket_id, int $id)
    {
        $item = \App\Models\TripExpenses::where('paket_id', $paket_id)->findOrFail($id);

        $this->service->delete($item);

        return redirect()
            ->route('keuangan.trip.expenses.index', $paket_id)
            ->with('success', 'Data berhasil dihapus.');
    }

    /* =====================================================
     | PDF
     ===================================================== */
    public function printPdf(int $paket_id)
    {
        $paket = PaketMaster::findOrFail($paket_id);
        $data  = $this->service->dataForPdf($paket_id);

        return Pdf::loadView('keuangan.trip-expenses.print', [
            'paket' => $paket,
            ...$data,
        ])
        ->setPaper('A4', 'portrait')
        ->stream('Biaya-Trip-' . str($paket->nama_paket)->slug('-') . '.pdf');
    }

    public function byKeberangkatan($paketId, $keberangkatanId)
    {
        $keberangkatan = Keberangkatan::with('paket')
            ->findOrFail($keberangkatanId);

        $expenses = $this->service
            ->listByKeberangkatan($keberangkatanId);

        $totalBiaya = $this->service
            ->totalByKeberangkatan($keberangkatanId);

        return view('keuangan.trip-expenses.by-keberangkatan', compact(
            'keberangkatan',
            'expenses',
            'totalBiaya'
        ));
    }


}


// namespace App\Http\Controllers\Keuangan;

// use App\Http\Controllers\Controller;
// use App\Services\Keuangan\TripExpensesService;
// use App\Models\PaketMaster;
// use App\Models\TripExpenses;
// use Illuminate\Http\Request;
// use Barryvdh\DomPDF\Facade\Pdf;

// class TripExpenseController extends Controller
// {
//     public function __construct(
//         protected TripExpensesService $service
//     ) {}

//     public function index(int $paket_id)
//     {
//         $paket = PaketMaster::findOrFail($paket_id);

//         return view('keuangan.trip-expenses.index', [
//             'paket'            => $paket,
//             'data'             => $this->service->listByPaket($paket_id),
//             'totalPengeluaran' => $this->service->totalByPaket($paket_id),
//             'totalJamaah'      => $this->service->totalJamaahByPaket($paket_id),
//         ]);
//     }

//     public function store(Request $request, int $paket_id)
//     {
//         $validated = $request->validate([
//             'kategori' => 'required|string|max:100',
//             'jumlah'   => 'required|numeric|min:0',
//             'tanggal'  => 'required|date',
//             'catatan'  => 'nullable|string',
//             'bukti'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
//         ]);

//         $this->service->create($paket_id, $validated);

//         return redirect()
//             ->route('keuangan.trip.expenses.index', $paket_id)
//             ->with('success', 'Biaya keberangkatan berhasil ditambahkan.');
//     }

//     public function update(Request $request, int $paket_id, int $id)
//     {
//         $item = TripExpenses::where('paket_id', $paket_id)->findOrFail($id);

//         $validated = $request->validate([
//             'kategori' => 'required|string|max:100',
//             'jumlah'   => 'required|numeric|min:0',
//             'tanggal'  => 'required|date',
//             'catatan'  => 'nullable|string',
//             'bukti'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
//         ]);

//         $this->service->update($item, $validated);

//         return redirect()
//             ->route('keuangan.trip.expenses.index', $paket_id)
//             ->with('success', 'Data berhasil diperbarui.');
//     }

//     public function destroy(int $paket_id, int $id)
//     {
//         $item = TripExpenses::where('paket_id', $paket_id)->findOrFail($id);

//         $this->service->delete($item);

//         return redirect()
//             ->route('keuangan.trip.expenses.index', $paket_id)
//             ->with('success', 'Data berhasil dihapus.');
//     }

//     public function printPdf(int $paket_id)
//     {
//         $paket = PaketMaster::findOrFail($paket_id);
//         $pdfData = $this->service->dataForPdf($paket_id);

//         return Pdf::loadView('keuangan.trip-expenses.print', array_merge(
//             ['paket' => $paket],
//             $pdfData
//         ))
//         ->setPaper('A4', 'portrait')
//         ->stream('Biaya-Trip-' . str($paket->nama_paket)->slug('-') . '.pdf');
//     }
// }


// use Illuminate\Http\Request;
// use App\Models\TripExpenses;
// use App\Models\PaketMaster;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Storage;
// use App\Models\Keberangkatan;
// use Barryvdh\DomPDF\Facade\Pdf;

// class TripExpenseController extends Controller
// {
//     /**
//      * List biaya per keberangkatan
//      */


//     public function index(Request $request, $paket_id)
//     {
//         $paket = PaketMaster::findOrFail($paket_id);

//         // ambil keberangkatan aktif / terbaru
//         $keberangkatan = Keberangkatan::withCount('jamaah')
//             ->where('id_paket_master', $paket_id)
//             ->orderByDesc('tanggal_berangkat')
//             ->first();

//         $data = TripExpenses::where('paket_id', $paket_id)
//             ->orderBy('tanggal', 'desc')
//             ->paginate(20);

//         $totalPengeluaran = TripExpenses::where('paket_id', $paket_id)->sum('jumlah');

//         $totalJamaah = $keberangkatan?->jamaah_count ?? 0;

//         return view('keuangan.trip-expenses.index', compact(
//             'paket',
//             'data',
//             'totalPengeluaran',
//             'totalJamaah'
//         ));
//     }

//     /**
//      * Form create
//      */
//     public function create($paket_id)
//     {
//         $paket = PaketMaster::findOrFail($paket_id);
//         return view('keuangan.trip-expenses.create', compact('paket'));
//     }

//     /**
//      * Save
//      */
//     public function store(Request $request, $paket_id)
//     {
//         $request->validate([
//             'kategori' => 'required|string|max:100',
//             'jumlah'   => 'required|numeric|min:0',
//             'tanggal'  => 'required|date',
//             'catatan'  => 'nullable|string',
//             'bukti'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
//         ]);

//         $file = null;

//         if ($request->hasFile('bukti')) {
//             $file = $request->file('bukti')->store('trip-expenses', 'public');
//         }

//         TripExpenses::create([
//             'paket_id'    => $paket_id,
//             'kategori'    => $request->kategori,
//             'jumlah'      => $request->jumlah,
//             'tanggal'     => $request->tanggal,
//             'catatan'     => $request->catatan,
//             'bukti'       => $file,
//             'dibuat_oleh' => Auth::id()
//         ]);

//         // ✅ FIX ROUTE
//         return redirect()->route('keuangan.trip.expenses.index', $paket_id)
//             ->with('success', 'Biaya keberangkatan berhasil ditambahkan.');
//     }

//     /**
//      * Detail
//      */
//     public function show($paket_id, $id)
//     {
//         $paket = PaketMaster::findOrFail($paket_id);
//         $item  = TripExpenses::where('paket_id', $paket_id)->findOrFail($id);

//         return view('keuangan.trip-expenses.show', compact('paket', 'item'));
//     }

//     /**
//      * Edit
//      */
//     public function edit($paket_id, $id)
//     {
//         $paket = PaketMaster::findOrFail($paket_id);
//         $item  = TripExpenses::where('paket_id', $paket_id)->findOrFail($id);

//         return view('keuangan.trip-expenses.edit', compact('paket', 'item'));
//     }

//     /**
//      * Update
//      */
//     public function update(Request $request, $paket_id, $id)
//     {
//         $item = TripExpenses::where('paket_id', $paket_id)->findOrFail($id);

//         $request->validate([
//             'kategori' => 'required|string|max:100',
//             'jumlah'   => 'required|numeric|min:0',
//             'tanggal'  => 'required|date',
//             'catatan'  => 'nullable|string',
//             'bukti'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
//         ]);

//         $file = $item->bukti;

//         if ($request->hasFile('bukti')) {
//             if ($file && Storage::disk('public')->exists($file)) {
//                 Storage::disk('public')->delete($file);
//             }
//             $file = $request->file('bukti')->store('trip-expenses', 'public');
//         }

//         $item->update([
//             'kategori' => $request->kategori,
//             'jumlah'   => $request->jumlah,
//             'tanggal'  => $request->tanggal,
//             'catatan'  => $request->catatan,
//             'bukti'    => $file,
//         ]);

//         // ✅ FIX ROUTE
//         return redirect()->route('keuangan.trip.expenses.index', $paket_id)
//             ->with('success', 'Data berhasil diperbarui.');
//     }

//     /**
//      * Delete
//      */
//     public function destroy($paket_id, $id)
//     {
//         $item = TripExpenses::where('paket_id', $paket_id)->findOrFail($id);

//         if ($item->bukti && Storage::disk('public')->exists($item->bukti)) {
//             Storage::disk('public')->delete($item->bukti);
//         }

//         $item->delete();

//         // ✅ FIX ROUTE
//         return redirect()->route('keuangan.trip.expenses.index', $paket_id)
//             ->with('success', 'Data berhasil dihapus.');
//     }

//     public function printPdf($paket_id)
//     {
//         // ===============================
//         // AMBIL DATA PAKET
//         // ===============================
//         $paket = PaketMaster::findOrFail($paket_id);

//         // ===============================
//         // AMBIL DATA BIAYA
//         // ===============================
//         $data = TripExpenses::where('paket_id', $paket_id)
//             ->orderBy('tanggal', 'asc')
//             ->get();

//         $totalPengeluaran = $data->sum('jumlah');

//         // ===============================
//         // HITUNG TOTAL JAMAAH
//         // - Ambil keberangkatan terbaru utk paket ini
//         // - Hitung jumlah jamaah real (bukan field manual)
//         // ===============================
//         $keberangkatan = Keberangkatan::withCount('jamaah')
//             ->where('id_paket_master', $paket_id)
//             ->orderByDesc('tanggal_berangkat')
//             ->first();

//         $totalJamaah = $keberangkatan?->jamaah_count ?? 0;

//         // ===============================
//         // GENERATE PDF
//         // ===============================
//         $pdf = Pdf::loadView('keuangan.trip-expenses.print', [
//             'paket'            => $paket,
//             'data'             => $data,
//             'totalPengeluaran' => $totalPengeluaran,
//             'totalJamaah'      => $totalJamaah,
//         ])->setPaper('A4', 'portrait');

//         // ===============================
//         // STREAM PDF
//         // ===============================
//         return $pdf->stream(
//             'Biaya-Trip-' . str($paket->nama_paket)->slug('-') . '.pdf'
//         );
//     }

// }
