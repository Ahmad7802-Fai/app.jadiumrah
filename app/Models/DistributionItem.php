<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributionItem extends Model
{
    protected $table = 'distribution_items';

    protected $fillable = [
        'distribution_id',
        'item_id',
        'jumlah'
    ];

    public $timestamps = false;

    public function master()
    {
        return $this->belongsTo(DistributionMaster::class, 'distribution_id');
    }

    public function barang()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
