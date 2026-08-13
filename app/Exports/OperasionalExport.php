<?php
namespace App\Exports;

use App\Models\OperationalExpense;
use Maatwebsite\Excel\Concerns\FromCollection;

class OperasionalExport implements FromCollection
{
    protected $bulan;
    protected $tahun;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function collection()
    {
        return OperationalExpense::whereYear('tanggal', $this->tahun)
                ->whereMonth('tanggal', $this->bulan)
                ->get();
    }
}
