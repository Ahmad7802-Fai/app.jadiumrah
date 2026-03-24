<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Traits\BranchScope;
use App\Models\Traits\HasRoleHelpers;
class Jamaah extends Model
{
    use BranchScope, HasFactory, SoftDeletes, HasRoleHelpers;

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    protected $table = 'jamaahs';

    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNABLE
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'jamaah_code',
        'user_id',
        'branch_id',
        'agent_id', // users.id
        'source',
        'family_id',

        'nama_lengkap',
        'gender',
        'tanggal_lahir',
        'tempat_lahir',

        'nik',
        'passport_number',
        'seat_number',

        'phone',
        'email',

        'address',
        'city',
        'province',

        'is_active',
        'approval_status',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'tanggal_lahir' => 'date',
        'is_active'     => 'boolean',
        'deleted_at'    => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    /**
     * Website user owner
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function savingAccount()
    {
        return $this->hasOne(SavingAccount::class);
    }

    /**
     * Branch owner
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Agent owner (users.id)
     */
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Jamaah documents
     */
    public function documents()
    {
        return $this->hasMany(JamaahDocument::class, 'jamaah_id');
    }

    /**
     * Bookings
     */
    public function bookings()
    {
        return $this->belongsToMany(
            Booking::class,
            'booking_jamaah',
            'jamaah_id',
            'booking_id'
        )->withTimestamps();
    }

    /**
     * Payments
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'jamaah_id');
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

    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }

    public function scopeByAgent($query, $userId)
    {
        return $query->where('agent_id', $userId);
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getApprovalBadgeAttribute(): string
    {
        return match ($this->approval_status) {
            'approved' => 'success',
            'rejected' => 'danger',
            default    => 'warning',
        };
    }

    public function getBirthInfoAttribute(): string
    {
        if (!$this->tanggal_lahir) {
            return '-';
        }

        return $this->tempat_lahir . ', ' .
               $this->tanggal_lahir->format('d M Y');
    }

    public function getAgeAttribute(): ?int
    {
        return $this->tanggal_lahir?->age;
    }

    /*
    |--------------------------------------------------------------------------
    | BUSINESS LOGIC
    |--------------------------------------------------------------------------
    */

    /**
     * Jamaah can be booked
     */
    public function canBook(): bool
    {
        return $this->approval_status === 'approved'
            && $this->is_active;
    }

    /**
     * Check ownership for agent
     */
    public function ownedByAgent($user): bool
    {
        return $this->agent_id === $user->id;
    }

    /**
     * Check ownership for website user
     */
    public function ownedByUser($user): bool
    {
        return $this->user_id === $user->id;
    }

    public function scopeVisibleFor($query, $user)
    {
        if ($user->hasRole(['SUPERADMIN','ADMIN_PUSAT'])) {
            return $query;
        }

        if ($user->hasRole('ADMIN_CABANG')) {
            return $query->where('branch_id', $user->branch_id);
        }

        if ($user->hasRole('AGENT')) {
            return $query->where('agent_id', $user->id);
        }

        if ($user->hasRole('CUSTOMER')) {
            return $query->where('user_id', $user->id);
        }

        return $query->whereRaw('1=0');
    }

}