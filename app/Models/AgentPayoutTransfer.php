<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentPayoutTransfer extends Model
{
    protected $table = 'agent_payout_transfers';

    protected $fillable = [
        'payout_id',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'amount',
        'paid_at',
        'paid_by',
        'transfer_proof',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    /* ===============================
     | RELATIONS
     =============================== */
    public function payout()
    {
        return $this->belongsTo(
            AgentPayoutRequest::class,
            'payout_id'
        );
    }

    public function admin()
    {
        return $this->belongsTo(
            User::class,
            'paid_by'
        );
    }
}
