<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityLog extends Model
{
    protected $table = 'security_logs';

    public $timestamps = false; // karena cuma pakai created_at

    protected $fillable = [
        'jamaah_user_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'created_at',
    ];
}
