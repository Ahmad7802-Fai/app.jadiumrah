<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyBankAccount extends Model
{
    protected $fillable = [
        'company_profile_id',
        'bank_name',
        'account_number',
        'account_name',
        'purpose',
        'is_default',
        'is_active',
    ];

    public function company()
    {
        return $this->belongsTo(CompanyProfile::class, 'company_profile_id');
    }
}

