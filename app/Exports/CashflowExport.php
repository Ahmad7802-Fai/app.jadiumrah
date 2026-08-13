<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CashflowExport implements FromArray, WithHeadings
{
    protected $from;
    protected $to;
    protected $data;

    public function __construct($from, $to, $data)
    {
        $this->from = $from;
        $this->to = $to;
        $this->data = $data;
    }

    public function array(): array
    {
        // build rows
        return [
            ['LAPORAN CASHFLOW', "Periode: {$this->from} s/d {$this->to}"],
            [],
            ['KATEGORI','NOMINAL'],
            ['Cash In - Jamaah', $this->data['cashInJamaah']],
            ['Cash In - Layanan', $this->data['cashInLayanan']],
            ['Total Cash In', $this->data['totalCashIn']],
            [],
            ['Trip Expenses', $this->data['tripExpenses']],
            ['Vendor Payments', $this->data['vendorPayments']],
            ['Operational', $this->data['operational']],
            ['Total Cash Out', $this->data['totalCashOut']],
            [],
            ['Net Cashflow', $this->data['netCashflow']],
        ];
    }

    public function headings(): array
    {
        return [];
    }
}
