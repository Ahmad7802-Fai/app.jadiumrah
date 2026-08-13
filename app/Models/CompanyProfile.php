<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;


class CompanyProfile extends Model
{
    use HasFactory;

    protected $table = 'company_profiles';

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

        'bank_name',
        'bank_account_name',
        'bank_account_number',

        'invoice_footer',
        'letter_footer',

        'signature_name',
        'signature_position',

        'is_active',
    ];

    /* ===============================
     | SCOPES
     =============================== */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /* ===============================
     | ACCESSORS (OPTIONAL BUT NICE)
     =============================== */

    public function getLogoUrlAttribute()
    {
        return $this->logo
            ? asset('storage/' . $this->logo)
            : asset('images/default-logo.png');
    }

    public function getLogoInvoiceUrlAttribute()
    {
        return $this->logo_invoice
            ? asset('storage/' . $this->logo_invoice)
            : $this->logo_url;
    }

    protected static function booted()
    {
        static::saved(fn () => Cache::forget('company_profile_active'));
        static::deleted(fn () => Cache::forget('company_profile_active'));
    }

    public function bankAccounts()
    {
        return $this->hasMany(CompanyBankAccount::class);
    }

    public function defaultBank(string $purpose = 'invoice')
    {
        return $this->bankAccounts()
            ->where('purpose', $purpose)
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();
    }


}
