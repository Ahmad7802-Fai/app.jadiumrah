<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamaahAudit extends Model
{
    use HasFactory;

    protected $table = 'jamaah_audits';

    protected $fillable = [
        'jamaah_id',
        'action',
        'old_data',
        'new_data',
        'performed_by',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    /* =====================================================
     | RELATIONS
     ===================================================== */

    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class, 'jamaah_id');
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /* =====================================================
     | SCOPES (OPTIONAL, TAPI BERGUNA)
     ===================================================== */

    public function scopeForJamaah($query, int $jamaahId)
    {
        return $query->where('jamaah_id', $jamaahId);
    }

    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }
    public function changes(): array
    {
        if (empty($this->old_data) || empty($this->new_data)) {
            return [];
        }

        return collect($this->new_data)
            ->diffAssoc($this->old_data)
            ->map(function ($newValue, $key) {
                return [
                    'old' => $this->old_data[$key] ?? null,
                    'new' => $newValue,
                ];
            })
            ->toArray();
    }

    public function audits()
    {
        return $this->hasMany(JamaahAudit::class, 'jamaah_id')
            ->latest();
    }

}
