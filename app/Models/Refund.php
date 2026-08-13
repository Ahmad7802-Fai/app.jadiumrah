<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Refund extends Model
{
    protected $fillable = [
        'payment_id',
        'booking_id',
        'branch_id',
        'refund_code',
        'amount',
        'reason',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
