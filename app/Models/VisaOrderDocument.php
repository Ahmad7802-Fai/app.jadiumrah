<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class VisaOrderDocument extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_KTP = 'ktp';
    public const TYPE_KK = 'kk';
    public const TYPE_PASSPORT = 'passport';
    public const TYPE_PHOTO = 'photo';
    public const TYPE_TICKET = 'ticket';
    public const TYPE_HOTEL_BOOKING = 'hotel_booking';
    public const TYPE_TRANSPORT_BOOKING = 'transport_booking';
    public const TYPE_OTHER = 'other';

    protected $fillable = [
        'visa_order_id',
        'visa_order_traveler_id',
        'document_type',
        'document_name',
        'file_path',
        'file_disk',
        'file_name',
        'mime_type',
        'file_size',
        'is_verified',
        'verified_at',
        'verified_by',
        'note',
    ];

    protected $casts = [
        'visa_order_id' => 'integer',
        'visa_order_traveler_id' => 'integer',
        'file_size' => 'integer',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'verified_by' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function order()
    {
        return $this->belongsTo(VisaOrder::class, 'visa_order_id');
    }

    public function traveler()
    {
        return $this->belongsTo(VisaOrderTraveler::class, 'visa_order_traveler_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('is_verified', true);
    }

    public function scopePendingVerification(Builder $query): Builder
    {
        return $query->where('is_verified', false);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        return Storage::disk($this->file_disk ?: 'public')->url($this->file_path);
    }
}