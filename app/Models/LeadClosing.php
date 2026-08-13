<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadClosing extends Model
{
    protected $table = 'lead_closings';

    public const STATUS_DRAFT    = 'DRAFT';
    public const STATUS_APPROVED = 'APPROVED';
    public const STATUS_REJECTED = 'REJECTED';

    protected $fillable = [
        'lead_id',
        'jamaah_id',
        'agent_id',
        'branch_id',
        'nominal_dp',
        'total_paket',
        'status',
        'closed_at',
        'catatan',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
        'nominal_dp' => 'decimal:2',
        'total_paket' => 'decimal:2',
    ];

    /* =====================================================
     | RELATIONS
     ===================================================== */

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class);
    }
}
