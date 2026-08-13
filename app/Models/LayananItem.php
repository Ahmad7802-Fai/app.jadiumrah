<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LayananItem extends Model
{
    protected $table = 'layanan_item';

    protected $fillable = [
        'id_layanan_master',
        'nama_item',
        'harga',

        // === F7 FIX ===
        'tipe',
        'durasi_hari_default',

        'satuan',
        'vendor',
        'tanggal_mulai',
        'tanggal_selesai',
        'currency',
        'status'
    ];

    public function master()
    {
        return $this->belongsTo(LayananMaster::class, 'id_layanan_master');
    }

    public function transaksiItems()
    {
        return $this->hasMany(LayananTransaksiItem::class, 'id_layanan_item');
    }

    /** Apakah item ini hotel? */
    public function isHotel()
    {
        return $this->tipe === 'hotel';
    }
}
