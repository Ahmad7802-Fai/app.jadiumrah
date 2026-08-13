<?php

namespace App\Services\Keuangan;

use App\Models\TripExpenses;
use App\Models\Keberangkatan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TripExpensesService
{
    /* =====================================================
     | LIST BIAYA PER PAKET
     ===================================================== */
    public function listByPaket(int $paketId)
    {
        return TripExpenses::where('paket_id', $paketId)
            ->orderByDesc('tanggal')
            ->paginate(20);
    }

    /* =====================================================
     | TOTAL BIAYA PER PAKET
     ===================================================== */
    public function totalByPaket(int $paketId): int
    {
        return (int) TripExpenses::where('paket_id', $paketId)->sum('jumlah');
    }

    /* =====================================================
     | TOTAL JAMAAH (REAL)
     ===================================================== */
    public function totalJamaahByPaket(int $paketId): int
    {
        $keberangkatan = Keberangkatan::withCount('jamaah')
            ->where('id_paket_master', $paketId)
            ->orderByDesc('tanggal_berangkat')
            ->first();

        return (int) ($keberangkatan?->jamaah_count ?? 0);
    }

    /* =====================================================
     | CREATE BIAYA
     ===================================================== */
    public function create(int $paketId, array $data): TripExpenses
    {
        if (!empty($data['bukti'])) {
            $data['bukti'] = $data['bukti']->store('trip-expenses', 'public');
        }

        return TripExpenses::create([
            'paket_id'         => $paketId,
            'keberangkatan_id' => $data['keberangkatan_id'], // 🔥 WAJIB
            'kategori'         => $data['kategori'],
            'jumlah'           => $data['jumlah'],
            'tanggal'          => $data['tanggal'],
            'catatan'          => $data['catatan'] ?? null,
            'bukti'            => $data['bukti'] ?? null,
            'dibuat_oleh'      => Auth::id(),
        ]);
    }


    /* =====================================================
     | UPDATE BIAYA
     ===================================================== */
    public function update(TripExpenses $item, array $data): TripExpenses
    {
        $file = $item->bukti;

        if (!empty($data['bukti'])) {
            if ($file && Storage::disk('public')->exists($file)) {
                Storage::disk('public')->delete($file);
            }
            $file = $data['bukti']->store('trip-expenses', 'public');
        }

        $item->update([
            'kategori' => $data['kategori'],
            'jumlah'   => $data['jumlah'],
            'tanggal'  => $data['tanggal'],
            'catatan'  => $data['catatan'] ?? null,
            'bukti'    => $file,
        ]);

        return $item;
    }

    /* =====================================================
     | DELETE BIAYA
     ===================================================== */
    public function delete(TripExpenses $item): void
    {
        if ($item->bukti && Storage::disk('public')->exists($item->bukti)) {
            Storage::disk('public')->delete($item->bukti);
        }

        $item->delete();
    }

    /* =====================================================
     | DATA UNTUK PDF
     ===================================================== */
    public function dataForPdf(int $paketId): array
    {
        $data = TripExpenses::where('paket_id', $paketId)
            ->orderBy('tanggal')
            ->get();

        return [
            'data' => $data,
            'totalPengeluaran' => (int) $data->sum('jumlah'),
            'totalJamaah'      => $this->totalJamaahByPaket($paketId),
        ];
    }

        /* =====================================================
     | TOTAL BIAYA PER KEBERANGKATAN
     ===================================================== */
    public function totalByKeberangkatan(int $keberangkatanId): int
    {
        return (int) TripExpenses::where('keberangkatan_id', $keberangkatanId)
            ->sum('jumlah');
    }

    /* =====================================================
     | LIST BIAYA PER KEBERANGKATAN (OPTIONAL)
     ===================================================== */
    public function listByKeberangkatan(int $keberangkatanId)
    {
        return TripExpenses::where('keberangkatan_id', $keberangkatanId)
            ->orderBy('tanggal')
            ->get();
    }
}
