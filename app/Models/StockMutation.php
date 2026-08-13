<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMutation extends Model
{
    protected $table = 'stock_mutations';

    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'tanggal',
        'tipe',
        'jumlah',
        'keterangan',
        'sumber',
        'referensi_id',
    ];

    public function barang()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}

