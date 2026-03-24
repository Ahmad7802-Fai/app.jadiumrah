<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketingCampaign extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'target_revenue',
        'budget_marketing',
        'status',
    ];

    protected $casts = [
        'start_date'       => 'date',
        'end_date'         => 'date',
        'target_revenue'   => 'decimal:2',
        'budget_marketing' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    // Campaign → Many Pakets (Pivot)
    public function pakets(): BelongsToMany
    {
        return $this->belongsToMany(
            Paket::class,
            'campaign_paket',
            'marketing_campaign_id',
            'paket_id'
        )->withTimestamps();
    }

    // Campaign → Many Bookings
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'marketing_campaign_id');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function getRevenueAttribute(): float
    {
        return $this->bookings()
            ->where('status', 'confirmed')
            ->sum('total_amount');
    }

    public function getRoiAttribute(): float
    {
        if ($this->budget_marketing <= 0) {
            return 0;
        }

        return (($this->revenue - $this->budget_marketing)
                / $this->budget_marketing) * 100;
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && now()->between($this->start_date, $this->end_date);
    }
}