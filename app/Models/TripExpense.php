<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripExpense extends Model
{
    use HasFactory;

    protected $table = 'trip_expenses';
    protected $primaryKey = 'id';

    protected $fillable = [
        'paket_id',
        'kategori',
        'jumlah',
        'tanggal',
        'catatan',
        'bukti',
        'dibuat_oleh',
    ];

    protected $casts = [
        'jumlah'      => 'integer',
        'tanggal'     => 'date',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    // Relasi ke paket umrah
    public function paket()
    {
        return $this->belongsTo(\App\Models\PaketUmrah::class, 'paket_id');
    }

    // User pembuat data
    public function dibuatOleh()
    {
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getBuktiUrlAttribute()
    {
        if (!$this->bukti) {
            return null;
        }
        return asset('uploads/trip_expenses/' . $this->bukti);
    }
}
