<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Traits\HasRoleHelpers;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use MustVerifyEmail, HasApiTokens, HasFactory, Notifiable, HasRoles, HasRoleHelpers;

    // Spatie Permission guard
    protected $guard_name = 'web';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'name',
        'email',
        'password',
        'branch_id',
        
    ];

    /*
    |--------------------------------------------------------------------------
    | Hidden
    |--------------------------------------------------------------------------
    */

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function agentProfile()
    {
        return $this->hasOne(Agent::class, 'user_id');
    }

    public function jamaahs()
    {
        return $this->hasMany(Jamaah::class, 'agent_id');
    }

    public function jamaahProfile()
    {
        return $this->hasOne(Jamaah::class,'user_id');
    }

    public function websiteJamaahs()
    {
        return $this->hasMany(Jamaah::class, 'user_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'agent_id');
    }

    public function savingAccount()
    {
        return $this->hasOne(SavingAccount::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ROLE HELPERS
    |--------------------------------------------------------------------------
    */

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('SUPERADMIN');
    }

    public function isAdminPusat(): bool
    {
        return $this->hasRole('ADMIN_PUSAT');
    }

    public function isAdminCabang(): bool
    {
        return $this->hasRole('ADMIN_CABANG');
    }

    public function isAgent(): bool
    {
        return $this->hasRole('AGENT');
    }

    public function isJamaah(): bool
    {
        return $this->hasRole('JAMAAH');
    }

    /*
    |--------------------------------------------------------------------------
    | Ownership helpers
    |--------------------------------------------------------------------------
    */

    public function canChooseBranch(): bool
    {
        return $this->hasRole([
            'SUPERADMIN',
            'ADMIN_PUSAT'
        ]);
    }

    public function canChooseAgent(): bool
    {
        return $this->hasRole([
            'SUPERADMIN',
            'ADMIN_PUSAT',
            'ADMIN_CABANG'
        ]);
    }
}