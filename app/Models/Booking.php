<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Booking extends Model
{
    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */
    protected $table = 'bookings';

    /*
    |--------------------------------------------------------------------------
    | AUTO LOAD
    |--------------------------------------------------------------------------
    */
    protected $with = ['paket', 'departure'];

    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNMENT
    |--------------------------------------------------------------------------
    */
    protected $fillable = [

        'booking_code',
        'invoice_number',

        'paket_id',
        'paket_departure_id',

        'branch_id',
        'agent_id',
        'user_id',
        'created_by',

        'room_type',
        'qty',

        // 🔥 PRICE SNAPSHOT
        'price_per_person_snapshot',
        'original_price_snapshot',
        'discount_snapshot',
        'promo_label_snapshot',

        'total_amount',
        'paid_amount',

        'status',
        'expired_at',

        'marketing_campaign_id',
        'voucher_id',
        'voucher_discount',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'price_per_person_snapshot' => 'decimal:2',
        'original_price_snapshot'   => 'decimal:2',
        'discount_snapshot'         => 'decimal:2',

        'total_amount'   => 'decimal:2',
        'paid_amount'    => 'decimal:2',
        'voucher_discount' => 'decimal:2',

        'expired_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | ROUTE KEY
    |--------------------------------------------------------------------------
    */
    public function getRouteKeyName()
    {
        return 'booking_code';
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeVisibleFor(Builder $query, $user): Builder
    {
        if ($user->hasRole(['SUPERADMIN', 'ADMIN_PUSAT'])) {
            return $query;
        }

        if ($user->hasRole('ADMIN_CABANG')) {
            return $query->where('branch_id', $user->branch_id);
        }

        if ($user->hasRole('AGENT')) {
            return $query->where(function ($q) use ($user) {
                $q->where('agent_id', $user->id)
                  ->orWhere('created_by', $user->id);
            });
        }

        if ($user->hasRole('JAMAAH')) {
            return $query->where('user_id', $user->id);
        }

        return $query->whereRaw('1=0');
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function paket()
    {
        return $this->belongsTo(Paket::class);
    }

    public function departure()
    {
        return $this->belongsTo(PaketDeparture::class, 'paket_departure_id');
    }

    public function jamaahs()
    {
        return $this->belongsToMany(
            Jamaah::class,
            'booking_jamaah',
            'booking_id',
            'jamaah_id'
        )
        ->withPivot([
            'room_type',
            'price',        // 🔥 WAJIB DIISI DARI SERVICE
            'seat_number',
        ])
        ->withTimestamps();
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'booking_id');
    }

    public function refunds()
    {
        return $this->hasManyThrough(
            Refund::class,
            Payment::class,
            'booking_id',
            'payment_id',
            'id',
            'id'
        );
    }

    public function costs()
    {
        return $this->hasMany(Cost::class);
    }

    public function addons()
    {
        return $this->hasMany(BookingAddon::class);
    }

    public function campaign()
    {
        return $this->belongsTo(MarketingCampaign::class, 'marketing_campaign_id');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function commissionLogs()
    {
        return $this->hasMany(CommissionLog::class);
    }

    /*
    |--------------------------------------------------------------------------
    | BUSINESS LOGIC
    |--------------------------------------------------------------------------
    */

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isExpired(): bool
    {
        return $this->expired_at && now()->greaterThan($this->expired_at);
    }

    public function isFullyPaid(): bool
    {
        return $this->total_paid >= $this->total_amount;
    }

    public function isOwnedBy($user): bool
    {
        if (!$user) return false;

        // SUPERADMIN / ADMIN PUSAT
        if ($user->hasRole(['SUPERADMIN', 'ADMIN_PUSAT'])) {
            return true;
        }

        // ADMIN CABANG
        if ($user->hasRole('ADMIN_CABANG')) {
            return (int) $this->branch_id === (int) $user->branch_id;
        }

        // AGENT
        if ($user->hasRole('AGENT')) {
            return
                (int) $this->agent_id === (int) $user->id ||
                (int) $this->created_by === (int) $user->id ||
                (int) $this->user_id === (int) $user->id;
        }

        // JAMAAH
        if ($user->hasRole('JAMAAH')) {
            return (int) $this->user_id === (int) $user->id;
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 FINANCIAL (SINGLE SOURCE OF TRUTH)
    |--------------------------------------------------------------------------
    */

    // 🔥 TOTAL PAID (REAL SOURCE)
    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()
            ->where('status', 'paid')
            ->sum('amount');
    }

    // 🔥 REMAINING
    public function getRemainingAttribute(): float
    {
        return max(0, (float) $this->total_amount - $this->total_paid);
    }

    // 🔥 PAYMENT STATUS
    public function getPaymentStatusAttribute(): string
    {
        if ($this->total_paid >= $this->total_amount && $this->total_amount > 0) {
            return 'lunas';
        }

        if ($this->total_paid > 0) {
            return 'partial';
        }

        return 'belum_lunas';
    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 PRICE HELPERS
    |--------------------------------------------------------------------------
    */

    public function getFinalPriceAttribute(): float
    {
        return (float) $this->price_per_person_snapshot;
    }

    public function getIsDiscountedAttribute(): bool
    {
        return (float) $this->discount_snapshot > 0;
    }
}