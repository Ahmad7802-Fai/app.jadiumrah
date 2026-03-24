<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'max_discount',
        'quota',
        'used',
        'expired_at',
        'is_active'
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'is_active'  => 'boolean',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function isValid(): bool
    {
        if (!$this->is_active) return false;

        if ($this->expired_at && now()->gt($this->expired_at)) return false;

        if ($this->quota && $this->used >= $this->quota) return false;

        return true;
    }

    public function calculateDiscount(float $amount): float
    {
        if ($this->type === 'fixed') {
            return min($this->value, $amount);
        }

        $discount = ($amount * $this->value) / 100;

        if ($this->max_discount) {
            return min($discount, $this->max_discount);
        }

        return $discount;
    }
}