<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch_id',
        'nama',
        'kode_agent',
        'slug',
        'phone',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // Agent profile belongs to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Agent belongs to branch
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /*
    |--------------------------------------------------------------------------
    | BOOKINGS (via user ownership)
    |--------------------------------------------------------------------------
    */

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'agent_id', 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | JAMAAH OWNED BY AGENT
    |--------------------------------------------------------------------------
    */

    public function jamaahs()
    {
        return $this->hasMany(Jamaah::class, 'agent_id', 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | PAYMENTS CREATED BY AGENT
    |--------------------------------------------------------------------------
    */

    public function payments()
    {
        return $this->hasMany(Payment::class, 'created_by', 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | COMMISSION LOG
    |--------------------------------------------------------------------------
    */

    public function commissionLogs()
    {
        return $this->hasMany(CommissionLog::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}