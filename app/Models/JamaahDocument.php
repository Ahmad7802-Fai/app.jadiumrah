<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class JamaahDocument extends Model
{
    use HasFactory;

    protected $table = 'jamaah_documents';

    protected $fillable = [
        'jamaah_id',
        'document_type',
        'file_path',
        'expired_at',
        'note',
    ];

    protected $casts = [
        'expired_at' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIP
    |--------------------------------------------------------------------------
    */

    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function getStatusAttribute()
    {
        if (!$this->expired_at) return 'valid';

        return now()->gt($this->expired_at) ? 'expired' : 'valid';
    }

    public function scopePassport($query)
    {
        return $query->where('document_type', 'passport');
    }

    public function scopeVisa($query)
    {
        return $query->where('document_type', 'visa');
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expired_at')
                     ->whereDate('expired_at', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expired_at')
                     ->whereBetween('expired_at', [
                         now(),
                         now()->addDays($days)
                     ]);
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

        return asset('storage/' . $this->file_path);
    }

    public function getIsExpiredAttribute(): bool
    {
        if (!$this->expired_at) {
            return false;
        }

        return $this->expired_at->isPast();
    }

    public function getExpiryBadgeAttribute(): string
    {
        if (!$this->expired_at) {
            return 'secondary';
        }

        if ($this->expired_at->isPast()) {
            return 'danger';
        }

        if ($this->expired_at->diffInDays(now()) <= 30) {
            return 'warning';
        }

        return 'success';
    }

    /*
    |--------------------------------------------------------------------------
    | BOOT
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::deleting(function ($document) {
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }
        });
    }
}