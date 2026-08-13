<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisaProduct extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_VISA_ONLY = 'visa_only';
    public const TYPE_VISA_BUNDLE = 'visa_bundle';
    public const TYPE_ADD_ON = 'add_on';

    protected $fillable = [
        'code',
        'name',
        'slug',
        'short_description',
        'description',
        'product_type',
        'price',
        'promo_price',
        'is_active',
        'is_featured',
        'sort_order',
        'features',
        'requirements',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'promo_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'features' => 'array',
        'requirements' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function orders()
    {
        return $this->hasMany(VisaOrder::class, 'visa_product_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getFinalPriceAttribute(): float
    {
        return (float) ($this->promo_price ?? $this->price ?? 0);
    }
}