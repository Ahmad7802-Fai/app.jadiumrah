<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlightManifest extends Model
{
    protected $fillable = [
        'flight_id',
        'departure_id',
        'generated_at',
        'generated_by',
        'file_path',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }

    public function departure()
    {
        return $this->belongsTo(PaketDeparture::class, 'departure_id');
    }
}