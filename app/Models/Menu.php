<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role;

class Menu extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    protected $table = 'menus';

    /*
    |--------------------------------------------------------------------------
    | FILLABLE
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'label',
        'route',
        'icon',
        'permission',
        'section',
        'parent_id',
        'order',
        'is_active',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'is_active' => 'boolean',
        'order'     => 'integer',
        'parent_id' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    /**
     * Many-to-many ke Role (optional)
     */
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'role_menu',
            'menu_id',
            'role_id'
        );
    }

    /**
     * Parent Menu
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Direct Children
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('order');
    }

    /**
     * Recursive Children (Unlimited Level)
     */
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Only active
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Root menu only
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Ordered by section & order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('section')
                     ->orderBy('order');
    }

    /**
     * Visible for Role IDs (optional role-based filter)
     */
    public function scopeForRoles($query, $roleIds)
    {
        return $query->whereHas('roles', function ($q) use ($roleIds) {
            $q->whereIn('roles.id', $roleIds);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * Check if this menu is parent
     */
    public function getIsParentAttribute(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Safe route existence check
     */
    public function routeExists(): bool
    {
        if (!$this->route) {
            return false;
        }

        return \Route::has($this->route);
    }
}