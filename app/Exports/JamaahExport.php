<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JamaahExport implements FromCollection, WithHeadings
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($j) {
            return [
                'Nama Jamaah'   => $j->nama_lengkap,
                'NIK'           => $j->nik,
                'Keberangkatan' => $j->keberangkatan->kode_keberangkatan ?? '-',
                'Tanggal'       => $j->keberangkatan->tanggal_berangkat ?? '-',
                'Paket'         => $j->paket,
                'Tipe Kamar'    => $j->tipe_kamar,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama Jamaah',
            'NIK',
            'Keberangkatan',
            'Tanggal Berangkat',
            'Paket',
            'Tipe Kamar',
        ];
    }
}
