<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashSale extends Model
{
    protected $fillable = [
        'paket_id',
        'discount_type',
        'value',
        'start_at',
        'end_at',
        'seat_limit',
        'used_seat',
        'is_active'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
        'is_active'=> 'boolean',
    ];

    public function paket()
    {
        return $this->belongsTo(Paket::class);
    }

    public function isRunning(): bool
    {
        return $this->is_active
            && now()->between($this->start_at, $this->end_at)
            && (!$this->seat_limit || $this->used_seat < $this->seat_limit);
    }

    public function calculateDiscount(float $price): float
    {
        if ($this->discount_type === 'fixed') {
            return min($this->value, $price);
        }

        return ($price * $this->value) / 100;
    }
}