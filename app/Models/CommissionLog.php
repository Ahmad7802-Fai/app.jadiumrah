<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionLog extends Model
{
    protected $fillable = [
        'commission_scheme_id',
        'booking_id',
        'jamaah_id',      // 🔥 WAJIB TAMBAH INI
        'branch_id',
        'agent_id',
        'company_amount',
        'branch_amount',
        'agent_amount',
    ];

    protected $casts = [
        'company_amount' => 'decimal:2',
        'branch_amount'  => 'decimal:2',
        'agent_amount'   => 'decimal:2',
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

    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function payoutItems()
    {
        return $this->hasMany(CommissionPayoutItem::class);
    }

    public function payouts()
    {
        return $this->belongsToMany(
            CommissionPayout::class,
            'commission_payout_items'
        )->withPivot('amount')
        ->withTimestamps();
    }

}