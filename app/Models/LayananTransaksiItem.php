<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LayananTransaksiItem extends Model
{
    protected $table = 'layanan_transaksi_items';

    protected $fillable = [
        'id_transaksi',
        'id_layanan_item',
        'qty',
        'days',        // ← WAJIB BARU
        'harga',
        'subtotal'
    ];

    protected $casts = [
        'qty'      => 'integer',
        'days'     => 'integer',   // ← WAJIB BARU
        'harga'    => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function transaksi()
    {
        return $this->belongsTo(LayananTransaksi::class, 'id_transaksi');
    }

    public function item()
    {
        return $this->belongsTo(LayananItem::class, 'id_layanan_item');
    }

    /** 
     * Hitung subtotal secara otomatis (harga × qty × days)
     */
    public function hitungSubtotal()
    {
        return $this->harga * $this->qty * $this->days;
    }

    /**
     * Format subtotal jadi Rupiah
     */
    public function getSubtotalFormattedAttribute()
    {
        return "Rp " . number_format($this->subtotal, 0, ',', '.');
    }
}
