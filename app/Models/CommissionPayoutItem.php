<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommissionPayoutItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'commission_payout_id',
        'commission_log_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function payout()
    {
        return $this->belongsTo(CommissionPayout::class, 'commission_payout_id');
    }

    public function log()
    {
        return $this->belongsTo(CommissionLog::class, 'commission_log_id');
    }
}