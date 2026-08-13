<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAuditLog extends Model
{
    protected $table = 'ticket_audit_logs';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'action',
        'before',
        'after',
        'actor_id',
        'actor_role',
        'ip_address',
        'user_agent',
    ];

    public $timestamps = false;

    protected $casts = [
        'before' => 'array',
        'after'  => 'array',
    ];
}
