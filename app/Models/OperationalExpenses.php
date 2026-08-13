<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationalExpenses extends Model
{
    use HasFactory;

    protected $table = 'operational_expenses';
    protected $primaryKey = 'id';

    protected $fillable = [
        'kategori',
        'deskripsi',
        'jumlah',
        'tanggal',
        'bukti',
        'dibuat_oleh',
    ];

    protected $casts = [
        'jumlah'    => 'integer',
        'tanggal'   => 'date',
        'created_at'=> 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    // Pengeluaran operasional dibuat oleh user
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    // Format jumlah ke tampilan Rupiah
    public function getJumlahFormattedAttribute()
    {
        return number_format($this->jumlah);
    }

    // Format tanggal untuk UI
    public function getTanggalIndoAttribute()
    {
        return $this->tanggal
            ? $this->tanggal->format('d M Y')
            : '-';
    }

    // Akses langsung nama user pembuat
    public function getDibuatOlehNamaAttribute()
    {
        return $this->creator->name ?? '-';
    }
}
