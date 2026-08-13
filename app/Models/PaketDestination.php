<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketDestination extends Model
{
    protected $fillable = [
        'paket_id',
        'destination_id',
        'day_order',
        'note',
    ];

    public function paket()
    {
        return $this->belongsTo(Paket::class);
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }
}