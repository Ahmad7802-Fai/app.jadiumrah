<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Support\AccessScope;
use App\Models\Agent;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'username',
        'branch_id',
        'agent_id', // ✅ WAJIB
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /* =====================================================
     | RELATIONS
     ===================================================== */

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Relasi Agent (khusus SALES Agent)
     * users.agent_id → agents.id
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    /* =====================================================
     | ACCESS CONTROL (GLOBAL)
     ===================================================== */

    public function scopeByAccess($query)
    {
        return AccessScope::apply($query, 'users');
    }

    /* =====================================================
     | ROLE HELPERS (FINAL & FAST)
     ===================================================== */

    /**
     * SUPERADMIN / SALES PUSAT
     */
    public function isPusat(): bool
    {
        if ($this->role === 'SUPERADMIN') {
            return true;
        }

        // SALES tapi bukan agent = pusat
        return $this->role === 'SALES' && is_null($this->agent_id);
    }

    /**
     * ADMIN CABANG
     */
    public function isCabang(): bool
    {
        return $this->role === 'ADMIN' && !is_null($this->branch_id);
    }

    /**
     * SALES AGENT
     */
    public function isAgent(): bool
    {
        return $this->role === 'SALES' && !is_null($this->agent_id);
    }

    /**
     * SALES (umum)
     */
    public function isSales(): bool
    {
        return $this->role === 'SALES';
    }

    /**
     * ROLE OPERASIONAL (bukan lead)
     */
    public function isOperational(): bool
    {
        return in_array($this->role, [
            'OPERATOR',
            'KEUANGAN',
            'INVENTORY',
        ], true);
    }

    /* =====================================================
     | STATUS
     ===================================================== */

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /* =====================================================
     | ROLE CHECKER (ALIAS SUPPORT)
     ===================================================== */

    public function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;

        foreach ($roles as $role) {
            switch ($role) {

                // ===== ROLE DB =====
                case 'SUPERADMIN':
                case 'ADMIN':
                case 'KEUANGAN':
                case 'INVENTORY':
                case 'OPERATOR':
                case 'SALES':
                    if ($this->role === $role) return true;
                    break;

                // ===== ROLE LOGICAL =====
                case 'AGENT':
                    if ($this->isAgent()) return true;
                    break;

                case 'CABANG':
                    if ($this->isCabang()) return true;
                    break;

                case 'PUSAT':
                    if ($this->isPusat()) return true;
                    break;
            }
        }

        return false;
    }
}

// namespace App\Models;

// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;
// use App\Support\AccessScope;
// use App\Models\Agent;
// class User extends Authenticatable
// {
//     use Notifiable;

//     protected $table = 'users';

//     protected $fillable = [
//         'nama',
//         'email',
//         'password',
//         'role',
//         'username',
//         'branch_id',
//         'is_active',
//     ];

//     protected $hidden = [
//         'password',
//         'remember_token',
//     ];

//     /* =====================================================
//      | RELATIONS
//      ===================================================== */

//     public function branch()
//     {
//         return $this->belongsTo(Branch::class);
//     }

//     /**
//      * Relasi agent (khusus SALES agent)
//      */
//     public function agent()
//     {
//         return $this->hasOne(Agent::class)
//             ->withoutGlobalScopes();
//     }

//     /* =====================================================
//      | ACCESS CONTROL (GLOBAL)
//      ===================================================== */

//     public function scopeByAccess($query)
//     {
//         return AccessScope::apply($query, 'users');
//     }

//     /* =====================================================
//      | ROLE HELPERS (FINAL)
//      ===================================================== */

//     /**
//      * PUSAT:
//      * - SUPERADMIN
//      * - SALES tapi bukan agent
//      */
//     public function isPusat(): bool
//     {
//         // SUPERADMIN selalu pusat
//         if ($this->role === 'SUPERADMIN') {
//             return true;
//         }

//         // SALES tapi TIDAK punya agent = SALES PUSAT
//         if ($this->role === 'SALES' && !$this->agent()->exists()) {
//             return true;
//         }

//         return false;
//     }


//     /**
//      * CABANG:
//      * - ADMIN
//      */
//     public function isCabang(): bool
//     {
//         return $this->role === 'ADMIN';
//     }

//     /**
//      * AGENT:
//      * - SALES
//      * - punya relasi agent
//      */


//     public function isAgent(): bool
//     {
//         return $this->role === 'SALES'
//             && Agent::withoutGlobalScopes()
//                 ->where('user_id', $this->id)
//                 ->exists();
//     }


//     /**
//      * SALES (umum)
//      */
//     public function isSales(): bool
//     {
//         return $this->role === 'SALES';
//     }

//     /**
//      * ROLE OPERASIONAL (bukan lead)
//      */
//     public function isOperational(): bool
//     {
//         return in_array($this->role, [
//             'OPERATOR',
//             'KEUANGAN',
//             'INVENTORY',
//         ]);
//     }

//     /* =====================================================
//      | STATUS
//      ===================================================== */

//     public function isActive(): bool
//     {
//         return (bool) $this->is_active;
//     }

//     public function hasRole(string|array $roles): bool
//     {
//         $roles = is_array($roles) ? $roles : [$roles];

//         foreach ($roles as $role) {

//             switch ($role) {

//                 // ======================
//                 // ROLE ASLI (DB)
//                 // ======================
//                 case 'SUPERADMIN':
//                     if ($this->role === 'SUPERADMIN') return true;
//                     break;

//                 case 'ADMIN':
//                     if ($this->role === 'ADMIN') return true;
//                     break;

//                 case 'KEUANGAN':
//                     if ($this->role === 'KEUANGAN') return true;
//                     break;

//                 case 'SALES':
//                     if ($this->role === 'SALES') return true;
//                     break;

//                 // ======================
//                 // ROLE LOGICAL (ALIAS)
//                 // ======================

//                 // AGENT = SALES + punya relasi agent
//                 case 'AGENT':
//                     if ($this->role === 'SALES' && $this->isAgent()) return true;
//                     break;

//                 // PUSAT = SUPERADMIN atau SALES tanpa agent
//                 case 'PUSAT':
//                     if ($this->isPusat()) return true;
//                     break;

//                 // CABANG = ADMIN
//                 case 'CABANG':
//                     if ($this->isCabang()) return true;
//                     break;
//             }
//         }

//         return false;
//     }

// }

// namespace App\Models;

// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;
// use App\Support\AccessScope;

// class User extends Authenticatable
// {
//     use Notifiable;

//     protected $table = 'users';

//     protected $fillable = [
//         'nama',
//         'email',
//         'password',
//         'role',
//         'username',
//         'branch_id',
//         'is_active',
//     ];

//     protected $hidden = [
//         'password',
//         'remember_token',
//     ];

//     /*
//     |--------------------------------------------------------------------------
//     | RELATIONS
//     |--------------------------------------------------------------------------
//     */

//     public function branch()
//     {
//        return $this->belongsTo(Branch::class);
//     }

//     public function agent()
//     {
//         return $this->hasOne(Agent::class);
//     }

//     /*
//     |--------------------------------------------------------------------------
//     | ACCESS CONTROL (GLOBAL)
//     |--------------------------------------------------------------------------
//     */
//     public function scopeByAccess($query)
//     {
//         return AccessScope::apply($query, 'users');
//     }

//     /*
//     |--------------------------------------------------------------------------
//     | HELPERS
//     |--------------------------------------------------------------------------
//     */

//     public function isRole(string $role): bool
//     {
//         return strtoupper($this->role) === strtoupper($role);
//     }

//     public function isActive(): bool
//     {
//         return (bool) $this->is_active;
//     }
// }
