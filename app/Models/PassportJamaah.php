<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PassportJamaah extends Model
{
    protected $table = 'passport_jamaah';

    protected $fillable = [
        'jamaah_id',
        'nomor_paspor',
        'tanggal_terbit_paspor',
        'tanggal_habis_paspor',
        'tempat_terbit_paspor',
        'negara_penerbit',
        'alamat_lengkap',
        'kecamatan',
        'kota',
        'provinsi',
        'kode_pos',
        'tujuan_imigrasi',
        'rekomendasi_paspor',
    ];

    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class);
    }

}
