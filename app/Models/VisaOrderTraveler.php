<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisaOrderTraveler extends Model
{
    use HasFactory, SoftDeletes;

    public const GENDER_MALE = 'male';
    public const GENDER_FEMALE = 'female';

    protected $fillable = [
        'visa_order_id',
        'full_name',
        'gender',
        'relationship',
        'is_main_applicant',
        'place_of_birth',
        'date_of_birth',
        'nationality',
        'nik',
        'passport_number',
        'passport_issue_date',
        'passport_expiry_date',
        'passport_issue_place',
        'address',
        'phone',
        'email',
        'father_name',
        'mother_name',
        'notes',
    ];

    protected $casts = [
        'visa_order_id' => 'integer',
        'is_main_applicant' => 'boolean',
        'date_of_birth' => 'date',
        'passport_issue_date' => 'date',
        'passport_expiry_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function order()
    {
        return $this->belongsTo(VisaOrder::class, 'visa_order_id');
    }

    public function documents()
    {
        return $this->hasMany(VisaOrderDocument::class, 'visa_order_traveler_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeMainApplicant(Builder $query): Builder
    {
        return $query->where('is_main_applicant', true);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getPassportExpiredAttribute(): bool
    {
        if (!$this->passport_expiry_date) {
            return false;
        }

        return $this->passport_expiry_date->isPast();
    }
}