<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use BelongsToBranch;
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'city',
        'address',
        'phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function agents()
    {
        return $this->hasMany(Agent::class);
    }

    public function companyRules()
    {
        return $this->hasMany(CommissionCompanyRule::class);
    }

    public function branchRules()
    {
        return $this->hasMany(CommissionBranchRule::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function costs()
    {
        return $this->hasMany(Cost::class);
    }

}