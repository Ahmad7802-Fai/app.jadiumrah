<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paket extends Model
{
    protected $fillable = [
        'name',
        'code',
        'slug',
        'departure_city',
        'departure_date',
        'return_date',
        'duration_days',
        'airline',
        'quota',
        'short_description',
        'description',
        'thumbnail',
        'gallery',
        'is_active',
        'is_published',
        'promo_label',
        'promo_value',
        'promo_type',
        'promo_expires_at',
    ];

    protected $casts = [
        'gallery' => 'array',
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'promo_expires_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function destinations()
    {
        return $this->hasMany(PaketDestination::class, 'paket_id');
    }

    public function itinerary()
    {
        return $this->hasMany(PaketDestination::class, 'paket_id');
    }

    public function departures()
    {
        return $this->hasMany(PaketDeparture::class, 'paket_id');
    }

    public function hotels()
    {
        return $this->hasMany(PaketHotel::class, 'paket_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'paket_id');
    }

    public function prices()
    {
        return $this->hasMany(PaketPrice::class, 'paket_id');
    }

    public function campaigns()
    {
        return $this->belongsToMany(
            MarketingCampaign::class,
            'campaign_paket',
            'paket_id',
            'marketing_campaign_id'
        )->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /*
    |--------------------------------------------------------------------------
    | IMAGE ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail) return null;

        if (str_starts_with($this->thumbnail, 'http')) {
            return $this->thumbnail;
        }

        return asset('storage/' . $this->thumbnail);
    }

    public function getGalleryUrlsAttribute(): array
    {
        $gallery = is_array($this->gallery) ? $this->gallery : [];

        return array_values(array_filter(array_map(function ($item) {

            if (!$item) return null;

            if (str_starts_with($item, 'http')) {
                return $item;
            }

            return asset('storage/' . $item);

        }, $gallery)));
    }

    /*
    |--------------------------------------------------------------------------
    | PRICE LOGIC
    |--------------------------------------------------------------------------
    */

    public function getPriceStartFromAttribute(): ?float
    {
        if ($this->relationLoaded('nextDeparture') && $this->nextDeparture) {
            return $this->nextDeparture->price_start_from;
        }

        $nextDeparture = $this->departures()
            ->where('is_active', true)
            ->where('is_closed', false)
            ->whereDate('departure_date', '>=', now()->startOfDay())
            ->orderBy('departure_date')
            ->withMin('prices', 'price')
            ->first();

        return $nextDeparture?->prices_min_price;
    }

    public function getPriceLabelAttribute(): ?string
    {
        if (!$this->price_start_from) return null;

        return 'Mulai dari Rp' . number_format((float) $this->price_start_from, 0, ',', '.');
    }

    /*
    |--------------------------------------------------------------------------
    | NEXT DEPARTURE
    |--------------------------------------------------------------------------
    */

    public function nextDeparture()
    {
        return $this->hasOne(PaketDeparture::class, 'paket_id')
            ->where('is_active', true)
            ->where('is_closed', false)
            ->whereDate('departure_date', '>=', now()->toDateString())
            ->whereRaw('COALESCE(quota, 0) > COALESCE(booked, 0)')
            ->orderBy('departure_date');
    }

    /*
    |--------------------------------------------------------------------------
    | PROMO LOGIC
    |--------------------------------------------------------------------------
    */

    public function getPromoAttribute(): ?array
    {
        if (!$this->promo_type || !$this->promo_value) {
            return null;
        }

        // expired
        if ($this->promo_expires_at && now()->gt($this->promo_expires_at)) {
            return null;
        }

        $label = match ($this->promo_type) {
            'discount' => 'Diskon Rp' . number_format($this->promo_value, 0, ',', '.'),
            'cashback' => 'Cashback Rp' . number_format($this->promo_value, 0, ',', '.'),
            default => $this->promo_label ?? null,
        };

        return [
            'label' => $label,
            'value' => (int) $this->promo_value,
            'type' => $this->promo_type,
            'expires_at' => optional($this->promo_expires_at)?->toISOString(),
        ];
    }

    public function getHasPromoAttribute(): bool
    {
        return $this->promo !== null;
    }

    public function getPriceAfterDiscountAttribute(): ?float
    {
        $price = $this->price_start_from;

        if (!$price) return null;

        if (!$this->promo_value || $this->promo_type !== 'discount') {
            return $price;
        }

        return max(0, $price - $this->promo_value); // ✅ anti minus
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if (!$this->promo_value || $this->promo_type !== 'discount') {
            return null;
        }

        if (!$this->price_start_from) return null;

        return (int) round(
            ($this->promo_value / $this->price_start_from) * 100
        );
    }
}