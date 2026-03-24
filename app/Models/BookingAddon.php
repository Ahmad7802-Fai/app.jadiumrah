<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingAddon extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'marketing_addon_id',
        'qty',
        'price',
        'cost_price',
        'total',
    ];

    protected $casts = [
        'price'      => 'decimal:2',
        'cost_price' => 'decimal:2',
        'total'      => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function addon()
    {
        return $this->belongsTo(MarketingAddon::class, 'marketing_addon_id');
    }
}