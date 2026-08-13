<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manifest extends Model
{
    use HasFactory;

    protected $table = 'manifest';

    protected $fillable = [
        'keberangkatan_id',
        'jamaah_id',
        'tipe_kamar',
        'nomor_kamar',
    ];

    // relasi
    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class, 'jamaah_id');
    }

    public function keberangkatan()
    {
        return $this->belongsTo(Keberangkatan::class, 'keberangkatan_id');
    }
}
