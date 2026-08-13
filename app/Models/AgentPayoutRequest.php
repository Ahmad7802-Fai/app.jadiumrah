<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\KomisiLogs;
class AgentPayoutRequest extends Model
{
    use HasFactory;

    protected $table = 'agent_payout_requests';

    protected $fillable = [
        'agent_id',
        'branch_id',
        'total_komisi',
        'total_item',
        'status',
        'requested_at',
        'approved_at',
        'paid_at',
        'requested_by',
        'approved_by',
        'paid_by',
        'note',
    ];

    protected $casts = [
        'total_komisi' => 'integer',
        'total_item'   => 'integer',
        'requested_at' => 'datetime',
        'approved_at'  => 'datetime',
        'paid_at'      => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    // =========================
    // RELATIONS
    // =========================

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function komisiLogs()
    {
        return $this->hasMany(KomisiLogs::class, 'payout_request_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    // =========================
    // STATUS CONSTANT
    // =========================

    public const STATUS_REQUESTED = 'requested';
    public const STATUS_APPROVED  = 'approved';
    public const STATUS_PAID      = 'paid';
    public const STATUS_REJECTED  = 'rejected';

    public function transfer()
    {
        return $this->hasOne(
            AgentPayoutTransfer::class,
            'payout_id'
        );
    }


    public function komisi()
    {
        return $this->hasMany(
            KomisiLogs::class,
            'payout_request_id'
        );
    }
}
