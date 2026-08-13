<?php

namespace App\Exports;

use App\Services\TabunganRekapService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TabunganRekapExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithColumnFormatting
{
    protected int $bulan;
    protected int $tahun;
    protected TabunganRekapService $rekapService;

    public function __construct(int $bulan, int $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->rekapService = app(TabunganRekapService::class);
    }

    /* ================= DATA ================= */
    public function collection()
    {
        $data = $this->rekapService->monthly($this->bulan, $this->tahun);

        return collect($data['rows']); // ✅ HANYA ROW
    }

    /* ================= HEADER ================= */
    public function headings(): array
    {
        return [
            'Nama Jamaah',
            'No Tabungan',
            'Saldo Awal',
            'Total Top Up',
            'Total Debit',
            'Saldo Akhir',
        ];
    }

    /* ================= ROW ================= */
    public function map($r): array
    {
        return [
            $r['jamaah']->nama_lengkap ?? '',
            $r['tabungan']->nomor_tabungan ?? '',
            $r['saldo_awal'],
            $r['total_topup'],
            $r['total_debit'],
            $r['saldo_akhir'],
        ];
    }

    /* ================= FORMAT ================= */
    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
