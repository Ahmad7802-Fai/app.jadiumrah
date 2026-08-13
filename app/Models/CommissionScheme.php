<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommissionScheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'year',
        'is_active',
    ];

    protected $casts = [
        'year'      => 'integer',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function companyRules()
    {
        return $this->hasMany(CommissionCompanyRule::class);
    }

    public function branchRules()
    {
        return $this->hasMany(CommissionBranchRule::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public static function active(): ?self
    {
        return static::where('is_active', true)->first();
    }
}