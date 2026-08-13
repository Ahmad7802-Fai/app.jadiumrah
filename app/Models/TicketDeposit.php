<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketDeposit extends Model
{
    protected $table = 'ticket_deposits';

    protected $fillable = [
        'pnr_id',
        'agent_id',
        'branch_id',
        'amount',
        'status',
        'bank_recipient',
        'sender',
        'transfer_date',
        'receipt_file',
        'source',
    ];

    public function pnr()
    {
        return $this->belongsTo(TicketPnr::class, 'pnr_id');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
