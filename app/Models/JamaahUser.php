<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
class JamaahUser extends Authenticatable
{
    use Notifiable;

    /**
     * =====================================
     * TABLE
     * =====================================
     */
    protected $table = 'jamaah_users';

    /**
     * =====================================
     * MASS ASSIGNMENT
     * =====================================
     */
    protected $fillable = [
        'jamaah_id',
        'email',
        'phone',
        'password',
        'password_changed_at',
        'is_active',
        'last_login_at',
    ];

    /**
     * =====================================
     * HIDDEN ATTRIBUTES
     * =====================================
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * =====================================
     * ATTRIBUTE CASTING (WAJIB)
     * =====================================
     */
    protected $casts = [
        'password_changed_at' => 'datetime',
        'last_login_at'       => 'datetime',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
        'is_active'           => 'boolean',
    ];

    /**
     * =====================================
     * RELATIONS
     * =====================================
     */
    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class, 'jamaah_id');
    }
    public function jamaahRaw()
    {
        return $this->belongsTo(Jamaah::class, 'jamaah_id')
            ->withoutGlobalScopes();
    }
    public function getAuthPassword()
    {
        return $this->password;
    }
    /**
     * =====================================
     * HELPERS (OPTIONAL TAPI BERGUNA)
     * =====================================
     */

    /**
     * Cek apakah password pernah diganti
     */
    public function hasChangedPassword(): bool
    {
        return !is_null($this->password_changed_at);
    }

    /**
     * Timestamp password terakhir diubah
     */
    public function passwordChangedTimestamp(): ?int
    {
        return $this->password_changed_at
            ? $this->password_changed_at->timestamp
            : null;
    }    
}
