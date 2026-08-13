<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pipeline extends Model
{
    protected $table = 'pipelines';

    protected $fillable = [
        'tahap',
        'urutan',
        'aktif',
    ];

    public function leads()
    {
        return $this->hasMany(Lead::class, 'pipeline_id');
    }

    /* =====================================================
     | DEFAULT PIPELINE (LEAD BARU)
     | Explicit: prospect
     ===================================================== */
    public static function default(): ?self
    {
        return static::where('aktif', 1)
            ->where('tahap', 'prospect')
            ->first();
    }

        public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
