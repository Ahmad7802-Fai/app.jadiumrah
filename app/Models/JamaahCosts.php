<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamaahCosts extends Model
{
    use HasFactory;

    protected $table = 'jamaah_costs';
    protected $primaryKey = 'id';

    protected $fillable = [
        'jamaah_id',
        'deskripsi',
        'jumlah',
        'tanggal',
    ];

    protected $casts = [
        'jumlah'     => 'integer',
        'tanggal'    => 'date',
        'created_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    // Biaya ini milik 1 jamaah
    public function jamaah()
    {
        return $this->belongsTo(\App\Models\Jamaah::class, 'jamaah_id');
    }
}
