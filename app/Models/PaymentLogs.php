<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentLogs extends Model
{
    use HasFactory;

    protected $table = 'payment_logs';
    protected $primaryKey = 'id';

    protected $fillable = [
        'payment_id',
        'action',
        'meta',
        'old_value',
        'new_value',
        'created_by',
        'created_at',
    ];

    public $timestamps = false; // karena tabel tidak punya updated_at

    protected $casts = [
        'meta'       => 'array',
        'old_value'  => 'array',
        'new_value'  => 'array',
        'created_at' => 'datetime',
    ];

    // =====================
    // ACTION
    // =====================
    public const ACTION_INPUT   = 'INPUT';
    public const ACTION_APPROVE = 'APPROVE';
    public const ACTION_REJECT  = 'REJECT';
    public const ACTION_UPDATE  = 'UPDATE';
    public const ACTION_DELETE  = 'DELETE';

    // =====================
    // CONTEXT
    // =====================
    public const CONTEXT_AGENT    = 'AGENT';
    public const CONTEXT_CABANG   = 'CABANG';
    public const CONTEXT_KEUANGAN = 'KEUANGAN';
    public const CONTEXT_SYSTEM   = 'SYSTEM';

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    // log → payment
    public function payment()
    {
        return $this->belongsTo(\App\Models\Payments::class, 'payment_id');
    }

    // log → user pembuat
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getMetaPrettyAttribute()
    {
        return json_encode($this->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeByPayment($query, $paymentId)
    {
        return $query->where('payment_id', $paymentId)->orderBy('id', 'desc');
    }

    public function scopeAction($query, $type)
    {
        return $query->where('action', $type);
    }
}
