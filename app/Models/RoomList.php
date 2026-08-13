<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomList extends Model
{
    use HasFactory;

    protected $table = 'room_list';
    protected $primaryKey = 'id';

    protected $fillable = [
        'keberangkatan_id',
        'nomor_kamar',
        'tipe_kamar', // Quad, Triple, Double
        'jamaah_id',
    ];

    protected $casts = [
        'keberangkatan_id' => 'integer',
        'jamaah_id'        => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // 1 kamar → belongs to 1 keberangkatan
    public function keberangkatan()
    {
        return $this->belongsTo(\App\Models\Keberangkatan::class, 'keberangkatan_id');
    }

    // 1 kamar → bisa diisi 1 jamaah (nullable)
    public function jamaah()
    {
        return $this->belongsTo(\App\Models\Jamaah::class, 'jamaah_id');
    }
}
