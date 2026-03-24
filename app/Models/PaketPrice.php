<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketPrice extends Model
{
    protected $fillable = [
        'paket_id',
        'room_type',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function paket()
    {
        return $this->belongsTo(Paket::class);
    }
}