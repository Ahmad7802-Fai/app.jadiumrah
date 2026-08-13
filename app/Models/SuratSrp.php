<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratSrp extends Model
{
    protected $table = 'surat_srp';

    // NON-AKTIFKAN timestamps karena tabel tidak punya updated_at
    public $timestamps = false;

    protected $fillable = [
        'jamaah_id',
        'nomor_surat',
        'created_at'
    ];
}
