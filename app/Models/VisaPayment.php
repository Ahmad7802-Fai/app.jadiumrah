<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisaPayment extends Model
{
    use HasFactory, SoftDeletes;

    public const METHOD_BANK_TRANSFER = 'bank_transfer';
    public const METHOD_CASH = 'cash';
    public const METHOD_GATEWAY = 'gateway';
    public const METHOD_MANUAL = 'manual';

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'visa_order_id',
        'payment_number',
        'payment_method',
        'amount',
        'payment_status',
        'reference_number',
        'bank_name',
        'account_name',
        'paid_at',
        'note',
        'confirmed_by',
        'confirmed_at',
    ];

    protected $casts = [
        'visa_order_id' => 'integer',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'confirmed_by' => 'integer',
        'confirmed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $payment) {
            if (blank($payment->payment_number)) {
                $payment->payment_number = self::generatePaymentNumber();
            }
        });

        static::saved(function (self $payment) {
            if ($payment->order) {
                $payment->order->recalculatePayment();
            }
        });

        static::deleted(function (self $payment) {
            if ($payment->order) {
                $payment->order->recalculatePayment();
            }
        });
    }

    public static function generatePaymentNumber(): string
    {
        $prefix = 'PAY-VISA-' . now()->format('Ymd');
        $lastId = (self::withTrashed()->max('id') ?? 0) + 1;

        return $prefix . '-' . str_pad((string) $lastId, 5, '0', STR_PAD_LEFT);
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function order()
    {
        return $this->belongsTo(VisaOrder::class, 'visa_order_id');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('payment_status', self::STATUS_PAID);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('payment_status', self::STATUS_PENDING);
    }
}