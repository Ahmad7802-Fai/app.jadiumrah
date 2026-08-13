<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visa extends Model
{
    protected $table = 'visa';

    public $timestamps = false; // ❗ FIX ERROR updated_at missing

    protected $fillable = [
        'jamaah_id',
        'keberangkatan_id',
        'status',
        'nomor_visa',
    ];

    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class, 'jamaah_id');
    }

    public function keberangkatan()
    {
        return $this->belongsTo(Keberangkatan::class, 'keberangkatan_id');
    }
}
