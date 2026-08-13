<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stock extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'stock';

    protected $fillable = [
        'item_id',
        'stok',
    ];

    public function barang()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function mutations()
    {
        return $this->hasMany(StockMutation::class, 'item_id', 'item_id');
    }
}
