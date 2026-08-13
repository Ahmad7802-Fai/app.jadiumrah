<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class LaporanKeuanganExport implements FromView
{
    public $revenues, $tripExpenses, $operational, $from, $to;

    public function __construct($revenues, $tripExpenses, $operational, $from, $to)
    {
        $this->revenues      = $revenues;
        $this->tripExpenses  = $tripExpenses;
        $this->operational   = $operational;
        $this->from          = $from;
        $this->to            = $to;
    }

    public function view(): View
    {
        return view('keuangan.laporan.excel.pnl-f4', [
            'revenues'     => $this->revenues,
            'tripExpenses' => $this->tripExpenses,
            'operational'  => $this->operational,
            'from'         => $this->from,
            'to'           => $this->to,
        ]);
    }
}
