<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class DistributionMaster extends Model
{
    protected $table = 'distribution_master';

    protected $fillable = [
        'tanggal',
        'tujuan',
        'catatan',
    ];

    public $timestamps = false;

    /**
     * CASTS
     * - tanggal otomatis jadi instance Carbon
     * - aman untuk format('Y-m-d') di blade
     */
    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * ACCESSOR + MUTATOR TANGGAL
     * - input string tetap aman
     * - null juga aman
     * - auto konversi ke Carbon
     */
    protected function tanggal(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? \Carbon\Carbon::parse($value) : null,
            set: fn ($value) =>
                $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : null
        );
    }

    /**
     * RELASI ITEM
     */
    public function items()
    {
        return $this->hasMany(DistributionItem::class, 'distribution_id');
    }
}
