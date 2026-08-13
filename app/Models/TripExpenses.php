<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TripExpenses extends Model
{
    protected $table = 'trip_expenses';

    protected $fillable = [
        'paket_id',
        'keberangkatan_id',
        'kategori',
        'jumlah',
        'tanggal',
        'catatan',
        'bukti',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah'  => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI PREMIUM
    |--------------------------------------------------------------------------
    */

    /** Trip Expense → Keberangkatan */
    public function keberangkatan()
    {
        return $this->belongsTo(\App\Models\Keberangkatan::class, 'paket_id');
    }

    /** Trip Expense → User Pembuat */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS (Auto Getter)
    |--------------------------------------------------------------------------
    */

    /** URL bukti */
    public function getBuktiUrlAttribute()
    {
        return $this->bukti ? asset('storage/' . $this->bukti) : null;
    }

    /** Format tanggal, untuk Blade premium */
    public function getTanggalFormatAttribute()
    {
        return $this->tanggal ? $this->tanggal->format('d M Y') : '-';
    }

    /** Format Rupiah */
    public function getJumlahFormatAttribute()
    {
        return 'Rp ' . number_format($this->jumlah, 0, ',', '.');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER PREMIUM
    |--------------------------------------------------------------------------
    */

    /** Hapus bukti file otomatis */
    public function deleteBukti()
    {
        if ($this->bukti && Storage::disk('public')->exists($this->bukti)) {
            Storage::disk('public')->delete($this->bukti);
        }
    }

    /** Saat delete model → hapus bukti */
    protected static function booted()
    {
        static::deleting(function ($expense) {
            $expense->deleteBukti();
        });
    }

    
}
