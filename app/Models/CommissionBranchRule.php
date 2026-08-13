<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionBranchRule extends Model
{
    protected $fillable = [
        'commission_scheme_id',
        'branch_id',
        'paket_id',
        'agent_percentage',
    ];

    protected $casts = [
        'agent_percentage' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function scheme()
    {
        return $this->belongsTo(CommissionScheme::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function paket()
    {
        return $this->belongsTo(Paket::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeForScheme($query, $schemeId)
    {
        return $query->where('commission_scheme_id', $schemeId);
    }
}