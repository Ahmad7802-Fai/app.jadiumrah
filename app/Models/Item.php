<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'satuan',
        'kategori',
        'harga_beli',
        'harga_jual',
    ];

    public function stock()
    {
        return $this->hasOne(Stock::class, 'item_id');
    }

    public function mutations()
    {
        return $this->hasMany(StockMutation::class, 'item_id');
    }

    public function distribusi()
    {
        return $this->hasMany(DistributionItem::class, 'item_id');
    }

}
