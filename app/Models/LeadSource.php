<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadSource extends Model
{
    protected $table = 'lead_sources';

    protected $fillable = [
        'nama_sumber',
        'tipe',
        'platform',
        'lokasi',
        'keterangan',
    ];

    public $timestamps = false; // 🔥 WAJIB
}
