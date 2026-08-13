<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MarketingAddon extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'selling_price',
        'cost_price',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'cost_price'    => 'decimal:2',
        'is_active'     => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function bookings()
    {
        return $this->belongsToMany(
            Booking::class,
            'booking_addons'
        )->withPivot([
            'qty',
            'price',
            'cost_price',
            'total'
        ])->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}