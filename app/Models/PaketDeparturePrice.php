<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketDeparturePrice extends Model
{
    protected $table = 'paket_departure_prices';

    protected $fillable = [
        'paket_departure_id',
        'room_type',
        'price',
        'promo_type',
        'promo_value',
        'promo_label',
        'promo_expires_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'promo_value' => 'decimal:2',
        'promo_expires_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | APPENDS (🔥 AUTO KE API)
    |--------------------------------------------------------------------------
    */
    protected $appends = [
        'final_price',
        'final_price_label',
        'discount',
        'discount_percent',
        'has_promo',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */
    public function departure()
    {
        return $this->belongsTo(PaketDeparture::class, 'paket_departure_id');
    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 CHECK PROMO ACTIVE
    |--------------------------------------------------------------------------
    */
    protected function isPromoActive(): bool
    {
        if (!$this->promo_value || !$this->promo_type) {
            return false;
        }

        if ($this->promo_expires_at && now()->gt($this->promo_expires_at)) {
            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 FINAL PRICE (CORE ENGINE)
    |--------------------------------------------------------------------------
    */
    public function getFinalPriceAttribute(): float
    {
        $price = (float) $this->price;

        if (!$this->isPromoActive()) {
            return $price;
        }

        // 🔥 PERCENT
        if ($this->promo_type === 'percent') {
            return round(
                $price - ($price * $this->promo_value / 100)
            );
        }

        // 🔥 FIXED
        if ($this->promo_type === 'fixed') {
            return max(0, round($price - $this->promo_value));
        }

        return $price;
    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 FORMAT PRICE
    |--------------------------------------------------------------------------
    */
    public function getFinalPriceLabelAttribute(): string
    {
        return 'Rp' . number_format($this->final_price, 0, ',', '.');
    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 DISCOUNT AMOUNT
    |--------------------------------------------------------------------------
    */
    public function getDiscountAttribute(): float
    {
        return max(0, (float) $this->price - $this->final_price);
    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 DISCOUNT PERCENT
    |--------------------------------------------------------------------------
    */
    public function getDiscountPercentAttribute(): int
    {
        if ($this->price <= 0) return 0;

        return (int) round(
            ($this->discount / $this->price) * 100
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 HAS PROMO
    |--------------------------------------------------------------------------
    */
    public function getHasPromoAttribute(): bool
    {
        return $this->isPromoActive();
    }
}