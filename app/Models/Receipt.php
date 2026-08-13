<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $table = 'receipts';
    protected $primaryKey = 'id';

    protected $fillable = [
        'jamaah_id',
        'payment_id',
        'invoice_id',
        'nomor_kwitansi',
        'tanggal',
        'jumlah',
        'wa_tujuan',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'jumlah' => 'integer'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    // Ke tabel jamaah
    public function jamaah()
    {
        return $this->belongsTo(\App\Models\Jamaah::class, 'jamaah_id');
    }

    // Ke tabel payments
    public function payment()
    {
        return $this->belongsTo(\App\Models\Payments::class, 'payment_id');
    }

    // Ke tabel invoices
    public function invoice()
    {
        return $this->belongsTo(\App\Models\Invoices::class, 'invoice_id');
    }

    // User yang membuat kwitansi
    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    // Format jumlah → 120000 → 120.000
    public function getJumlahFormattedAttribute()
    {
        return number_format($this->jumlah);
    }

    // Nomor kwitansi auto uppercase
    public function getNoKwitansiFormattedAttribute()
    {
        return strtoupper($this->nomor_kwitansi);
    }

    // Link WA otomatis
    public function getWaLinkAttribute()
    {
        if (!$this->wa_tujuan) return null;

        $text = urlencode("Kwitansi Pembayaran #{$this->nomor_kwitansi}");

        return "https://wa.me/{$this->wa_tujuan}?text={$text}";
    }
}
