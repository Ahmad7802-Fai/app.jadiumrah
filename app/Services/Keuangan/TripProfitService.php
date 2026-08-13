<?php

namespace App\Services\Keuangan;

use App\Models\Keberangkatan;
use App\Models\Payments;

class TripProfitService
{
    public function __construct(
        protected TripExpensesService $expenseService
    ) {}

    /* =====================================================
     | LIST PROFIT PER KEBERANGKATAN
     ===================================================== */
    public function list(?int $paketId = null, int $perPage = 20)
    {
        $query = Keberangkatan::query()
            ->with(['paket', 'jamaah'])
            ->orderByDesc('tanggal_berangkat');

        if ($paketId) {
            $query->where('id_paket_master', $paketId);
        }

        return $query->paginate($perPage)
            ->through(fn ($k) => $this->mapProfit($k));
    }

    /* =====================================================
     | DETAIL PROFIT 1 KEBERANGKATAN
     ===================================================== */
    public function calculate(int $keberangkatanId): array
    {
        $k = Keberangkatan::with(['paket', 'jamaah'])
            ->findOrFail($keberangkatanId);

        return $this->mapProfit($k);
    }

    /* =====================================================
     | MAP PROFIT DATA
     ===================================================== */
    protected function mapProfit(Keberangkatan $k): array
    {
        /* ===============================
        | REVENUE (VALID PAYMENT)
        =============================== */
        $revenue = Payments::query()
            ->where('status', 'valid')
            ->whereHas('jamaah', fn ($q) =>
                $q->where('id_keberangkatan', $k->id)
            )
            ->sum('jumlah');

        /* ===============================
        | TRIP COST (BY KEBERANGKATAN)
        =============================== */
        $tripCost = $this->expenseService
            ->totalByKeberangkatan($k->id);

        /* ===============================
        | JAMAAH COUNT
        =============================== */
        $jamaahCount = $k->jamaah()->count();

        /* ===============================
        | RESULT
        =============================== */
        return [
            'id'        => $k->id,
            'paket_id' => $k->id_paket_master,
            'kode'      => $k->kode_keberangkatan,
            'paket'     => $k->paket?->nama_paket ?? '-',
            'tanggal'   => $k->tanggal_berangkat,
            'jamaah'    => $jamaahCount,

            'revenue'   => (int) $revenue,
            'trip_cost' => (int) $tripCost,
            'profit'    => (int) ($revenue - $tripCost),
        ];
    }

}
