<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LayananMaster extends Model
{
    protected $table = 'layanan_master';

    protected $fillable = [
        'kode_layanan',
        'nama_layanan',
        'kategori',
        'deskripsi',
        'status'
    ];

    public function items()
    {
        return $this->hasMany(LayananItem::class, 'id_layanan_master');
    }
}
