<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LaporanPnlExport implements FromArray, WithStyles, WithColumnWidths, ShouldAutoSize
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return [

            // ================= HEADER =================
            ['LAPORAN LABA RUGI (PNL)'],
            ["Periode: {$this->data['bulanNama']} {$this->data['tahun']}"],
            [''],

            // ================= REVENUE =================
            ['PENDAPATAN'],
            ['Pendapatan Jamaah', $this->data['revenueJamaah']],
            ['Pendapatan Layanan', $this->data['revenueLayanan']],
            ['Total Pendapatan', $this->data['totalRevenue']],
            [''],

            // ================= HPP =================
            ['HARGA POKOK PENJUALAN (HPP)'],
            ['Biaya Trip', $this->data['tripExpenses']],
            ['Biaya Vendor', $this->data['vendorExpenses']],
            ['Total HPP', $this->data['hpp']],
            [''],

            // ================= GROSS PROFIT =================
            ['LABA KOTOR'],
            ['Laba Kotor (Gross Profit)', $this->data['grossProfit']],
            [''],

            // ================= OPERATING EXPENSE =================
            ['BEBAN OPERASIONAL'],
            ['Biaya Operasional', $this->data['operational']],
            ['Biaya Marketing', $this->data['marketing']],
            ['Total Beban Operasional', $this->data['operational'] + $this->data['marketing']],
            [''],

            // ================= NET PROFIT =================
            ['LABA BERSIH'],
            ['Laba Bersih (Net Profit)', $this->data['netProfit']],
            [''],

            // ================= FOOTER =================
            ['PT. JADI UMRAH INDONESIA — Dicetak otomatis'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $brandGreen = '0F6B47';

        /* ================= HEADER ================= */
        $sheet->getStyle('A1')->getFont()
            ->setBold(true)->setSize(16)->getColor()->setRGB($brandGreen);

        $sheet->getStyle('A2')->getFont()
            ->setBold(true)->setSize(12)->getColor()->setRGB($brandGreen);

        /* ================= SECTION TITLES ================= */
        foreach ([
            'A4',   // Pendapatan
            'A9',   // HPP
            'A14',  // Laba Kotor
            'A17',  // Beban Operasional
            'A22',  // Laba Bersih
        ] as $cell) {
            $sheet->getStyle($cell)->getFont()
                ->setBold(true)->setSize(12)->getColor()->setRGB($brandGreen);
        }

        /* ================= RUPIAH FORMAT ================= */
        $sheet->getStyle("B1:B{$highestRow}")
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        /* ================= BORDER ================= */
        $sheet->getStyle("A1:B{$highestRow}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle('thin');

        /* ================= FOOTER ================= */
        $sheet->getStyle("A{$highestRow}")
            ->getFont()->setItalic(true)->setSize(10);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 45,
            'B' => 25,
        ];
    }
}
