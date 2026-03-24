<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    protected $fillable = [
        'name',
        'brand_name',
        'logo',
        'logo_invoice',
        'logo_bw',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'province',
        'postal_code',
        'npwp',
        'npwp_name',
        'npwp_address',
        'invoice_footer',
        'letter_footer',
        'signature_name',
        'signature_position',
        'is_active',
    ];

    public function bankAccounts()
    {
        return $this->hasMany(CompanyBankAccount::class);
    }
}