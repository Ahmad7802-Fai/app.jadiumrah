<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JamaahLogs extends Model
{
    protected $table = 'jamaah_logs';

    protected $fillable = [
        'jamaah_id',
        'action',
        'meta',
        'created_by',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public $timestamps = false; // karena created_at manual di DB

    /* ============================================================
     | RELATIONS
     ============================================================ */

    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class, 'jamaah_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
