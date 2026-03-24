<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisaOrder extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_WAITING_DOCUMENTS = 'waiting_documents';
    public const STATUS_WAITING_PAYMENT = 'waiting_payment';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_UNPAID = 'unpaid';
    public const PAYMENT_PARTIAL = 'partial';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_FAILED = 'failed';
    public const PAYMENT_REFUNDED = 'refunded';

    protected $fillable = [
        'order_number',
        'user_id',
        'visa_product_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'total_travelers',
        'departure_date',
        'return_date',
        'departure_city',
        'destination_city',
        'order_status',
        'payment_status',
        'subtotal',
        'discount_amount',
        'admin_fee',
        'total_amount',
        'amount_paid',
        'remaining_amount',
        'customer_note',
        'admin_note',
        'submitted_at',
        'approved_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'visa_product_id' => 'integer',
        'total_travelers' => 'integer',
        'departure_date' => 'date',
        'return_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'admin_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $order) {
            if (blank($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }

            if (blank($order->remaining_amount)) {
                $total = (float) ($order->total_amount ?? 0);
                $paid = (float) ($order->amount_paid ?? 0);
                $order->remaining_amount = max($total - $paid, 0);
            }
        });

        static::updating(function (self $order) {
            $total = (float) ($order->total_amount ?? 0);
            $paid = (float) ($order->amount_paid ?? 0);
            $order->remaining_amount = max($total - $paid, 0);
        });
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'VISA-' . now()->format('Ymd');
        $lastId = (self::withTrashed()->max('id') ?? 0) + 1;

        return $prefix . '-' . str_pad((string) $lastId, 5, '0', STR_PAD_LEFT);
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(VisaProduct::class, 'visa_product_id');
    }

    public function travelers()
    {
        return $this->hasMany(VisaOrderTraveler::class, 'visa_order_id');
    }

    public function documents()
    {
        return $this->hasMany(VisaOrderDocument::class, 'visa_order_id');
    }

    public function payments()
    {
        return $this->hasMany(VisaPayment::class, 'visa_order_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(VisaStatusHistory::class, 'visa_order_id')->latest('id');
    }

    public function notes()
    {
        return $this->hasMany(VisaOrderNote::class, 'visa_order_id')->latest('id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->latest('id');
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $query->when($status, fn ($q) => $q->where('order_status', $status));
    }

    public function scopePaymentStatus(Builder $query, ?string $status): Builder
    {
        return $query->when($status, fn ($q) => $q->where('payment_status', $status));
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        return $query->when($keyword, function ($q) use ($keyword) {
            $q->where(function ($sub) use ($keyword) {
                $sub->where('order_number', 'like', "%{$keyword}%")
                    ->orWhere('customer_name', 'like', "%{$keyword}%")
                    ->orWhere('customer_email', 'like', "%{$keyword}%")
                    ->orWhere('customer_phone', 'like', "%{$keyword}%");
            });
        });
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function recalculatePayment(): void
    {
        $amountPaid = (float) $this->payments()
            ->where('payment_status', VisaPayment::STATUS_PAID)
            ->sum('amount');

        $total = (float) $this->total_amount;
        $remaining = max($total - $amountPaid, 0);

        $paymentStatus = self::PAYMENT_UNPAID;

        if ($amountPaid > 0 && $amountPaid < $total) {
            $paymentStatus = self::PAYMENT_PARTIAL;
        } elseif ($amountPaid >= $total && $total > 0) {
            $paymentStatus = self::PAYMENT_PAID;
        }

        $this->update([
            'amount_paid' => $amountPaid,
            'remaining_amount' => $remaining,
            'payment_status' => $paymentStatus,
        ]);
    }

    public function addStatusHistory(?string $fromStatus, string $toStatus, ?string $description = null, ?int $changedBy = null): VisaStatusHistory
    {
        return $this->statusHistories()->create([
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'description' => $description,
            'changed_by' => $changedBy,
            'changed_at' => now(),
        ]);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    public function isCompleted(): bool
    {
        return $this->order_status === self::STATUS_COMPLETED;
    }
}