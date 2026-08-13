<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketDeparture extends Model
{
    protected $fillable = [
        'paket_id',
        'departure_code',
        'flight_number',
        'meeting_point',
        'departure_date',
        'return_date',
        'quota',
        'booked',
        'is_active',
        'is_closed',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date'    => 'date',
        'quota'          => 'integer',
        'booked'         => 'integer',
        'is_active'      => 'boolean',
        'is_closed'      => 'boolean',
    ];

    public function paket()
    {
        return $this->belongsTo(Paket::class);
    }

    public function prices()
    {
        return $this->hasMany(PaketDeparturePrice::class, 'paket_departure_id');
    }

    public function getQuotaRemainingAttribute(): int
    {
        $quota = (int) ($this->quota ?? 0);
        $booked = (int) ($this->booked ?? 0);

        return max(0, $quota - $booked);
    }

    public function getOccupancyPercentageAttribute(): ?float
    {
        $quota = (int) ($this->quota ?? 0);
        $booked = (int) ($this->booked ?? 0);

        if ($quota <= 0) {
            return null;
        }

        return round(($booked / $quota) * 100, 2);
    }

    public function getIsAvailableAttribute(): bool
    {
        return (bool) $this->is_active
            && !(bool) $this->is_closed
            && $this->quota_remaining > 0
            && optional($this->departure_date)->startOfDay()->gte(now()->startOfDay());
    }

    public function getPriceStartFromAttribute(): ?float
    {
        if ($this->relationLoaded('prices')) {
            $minPrice = $this->prices->min('price');
            return $minPrice !== null ? (float) $minPrice : null;
        }

        $minPrice = $this->prices()->min('price');

        return $minPrice !== null ? (float) $minPrice : null;
    }

    public function getPriceLabelAttribute(): ?string
    {
        if ($this->price_start_from === null) {
            return null;
        }

        return 'Rp' . number_format($this->price_start_from, 0, ',', '.');
    }

        public function remainingQuota(): int
    {
        $quota = (int) ($this->quota ?? 0);

        // kalau ada kolom booked langsung pakai
        if (isset($this->booked)) {
            return max(0, $quota - (int) $this->booked);
        }

        // fallback kalau booking dihitung dari relasi
        if (method_exists($this, 'bookings')) {
            $booked = (int) $this->bookings()->count();
            return max(0, $quota - $booked);
        }

        return $quota;
    }
}