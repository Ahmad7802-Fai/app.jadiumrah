<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamaahKeberangkatan extends Model
{
    use HasFactory;

    protected $table = 'jamaah_keberangkatan';
    protected $primaryKey = 'id';

    protected $fillable = [
        'jamaah_id',
        'keberangkatan_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    // Setiap record pivot → milik 1 jamaah
    public function jamaah()
    {
        return $this->belongsTo(\App\Models\Jamaah::class, 'jamaah_id');
    }

    // Setiap record pivot → milik 1 keberangkatan
    public function keberangkatan()
    {
        return $this->belongsTo(\App\Models\Keberangkatan::class, 'keberangkatan_id');
    }
}
