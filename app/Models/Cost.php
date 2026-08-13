<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cost extends Model
{
    use HasFactory;

    protected $fillable = [
        'cost_category_id',
        'booking_id',
        'paket_departure_id',
        'branch_id',
        'amount',
        'description',
        'proof_file',
        'cost_date',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'cost_date'   => 'date',
        'approved_at' => 'datetime',
        'amount'      => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo(CostCategory::class, 'cost_category_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function departure()
    {
        return $this->belongsTo(PaketDeparture::class, 'paket_departure_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}