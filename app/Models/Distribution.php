<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    use HasFactory;

    protected $table = 'distribution';
    protected $primaryKey = 'id';

    protected $fillable = [
        'tanggal',
        'tujuan',
        'catatan',
    ];

    protected $casts = [
        'tanggal'    => 'datetime',
        'created_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    | Set sesuai tabel lain:
    | - distribution_items (1 distribution -> banyak item)
    |--------------------------------------------------------------------------
    */

    public function items()
    {
        return $this->hasMany(\App\Models\DistributionItems::class, 'distribution_id');
    }
}
