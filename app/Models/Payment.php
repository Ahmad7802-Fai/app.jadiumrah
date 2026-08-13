<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Payment extends Model
{
    use HasFactory;

    protected $appends = ['proof_url'];

    /*
    |--------------------------------------------------------------------------
    | AUTO LOAD RELATIONS
    |--------------------------------------------------------------------------
    */

    protected $with = [
        'booking'
    ];

    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNMENT
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'booking_id',
        'jamaah_id',
        'paket_departure_id',
        'branch_id',

        'payment_code',
        'reference_number',
        'invoice_number',
        'receipt_number',

        'type',
        'method',
        'channel',

        'amount',
        'fee_amount',
        'net_amount',

        'status',

        'paid_at',
        'note',

        'proof_file',

        'created_by',
        'approved_by',
        'approved_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',

        'paid_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | ENUM CONSTANTS
    |--------------------------------------------------------------------------
    */

    public const TYPES = [
        'dp',
        'cicilan',
        'pelunasan',
        'add_on',
        'upgrade',
        'adjustment',
    ];

    public const METHODS = [
        'cash',
        'transfer',
        'gateway',
        'edc',
    ];

    public const STATUSES = [
        'pending',
        'paid',
        'failed',
        'cancelled',
    ];

    /*
    |--------------------------------------------------------------------------
    | ROLE VISIBILITY
    |--------------------------------------------------------------------------
    */

    public function scopeVisibleFor(Builder $query, $user): Builder
    {
        if ($user->hasRole(['SUPERADMIN','ADMIN_PUSAT'])) {
            return $query;
        }

        /*
        |--------------------------------------------------------------------------
        | ADMIN CABANG
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('ADMIN_CABANG')) {
            return $query->where('branch_id',$user->branch_id);
        }

        /*
        |--------------------------------------------------------------------------
        | AGENT
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('AGENT')) {
            return $query->whereHas('booking', function ($q) use ($user) {
                $q->where('agent_id',$user->id);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | JAMAAH
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('JAMAAH')) {
            return $query->whereHas('booking', function ($q) use ($user) {
                $q->where('user_id',$user->id);
            });
        }

        return $query->whereRaw('1=0');
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function logs()
    {
        return $this->hasMany(PaymentLog::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class,'booking_id');
    }

    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class,'jamaah_id');
    }

    public function departure()
    {
        return $this->belongsTo(PaketDeparture::class,'paket_departure_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class,'branch_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class,'approved_by');
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class,'payment_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getProofUrlAttribute()
    {
        return $this->proof_file
            ? asset('storage/'.$this->proof_file)
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | BUSINESS HELPERS
    |--------------------------------------------------------------------------
    */

    public function canBeEdited(): bool
    {
        return $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where('status','pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status','paid');
    }

}