<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingBanner extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'mobile_image',
        'link',
        'link_type',
        'page',
        'position',
        'sort_order',
        'status',
        'is_active',
        'start_date',
        'end_date',
        'campaign_id',
        'target_role',
        'target_branch_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function campaign()
    {
        return $this->belongsTo(MarketingCampaign::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'target_branch_id');
    }

    public function isVisible(): bool
    {
        if (!$this->is_active || $this->status !== 'published') {
            return false;
        }

        if ($this->start_date && now()->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && now()->gt($this->end_date)) {
            return false;
        }

        return true;
    }
}