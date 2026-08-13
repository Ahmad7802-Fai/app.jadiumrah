<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

// MODELS
use App\Models\Agent;
use App\Models\Branch;
use App\Models\LeadSource;
use App\Models\LeadActivity;
use App\Models\LeadClosing;
use App\Models\Jamaah;

class Lead extends Model
{

    protected $fillable = [
        'nama',
        'no_hp',
        'email',
        'sumber_id',
        'channel',
        'branch_id','agent_id',
        'pipeline_id', // ✅ WAJIB
        'status','catatan',
        'created_by',
        'closed_at',
        'dropped_at',
        'drop_reason',
    ];


    /* =====================================================
     | RELATIONS
     ===================================================== */

    public function agent()
    {
        return $this->belongsTo(Agent::class)
            ->withoutGlobalScopes();
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function source()
    {
        return $this->belongsTo(LeadSource::class, 'sumber_id');
    }
    public function sumber()
    {
        return $this->belongsTo(LeadSource::class, 'sumber_id');
    }

    public function activities()
    {
        return $this->hasMany(LeadActivity::class)
            ->orderByDesc('created_at');
    }
    public function latestFollowUp()
    {
        return $this->hasOne(LeadActivity::class)->latestOfMany();
    }
    public function closing()
    {
        return $this->hasOne(LeadClosing::class);
    }
    public function jamaah()
    {
        return $this->hasOne(Jamaah::class, 'lead_id');
    }
    /* =====================================================
     | BUSINESS LOGIC
     ===================================================== */

    public function canSubmitClosing(): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        return
            $this->status === 'ACTIVE'
            && !$this->closing
            && $user->isAgent()
            && $this->agent_id === $user->agent_id;
    }

    public function isLocked(): bool
    {
        return $this->status === 'CLOSED';
    }

    public function isOverdue(): bool
    {
        if ($this->isLocked()) {
            return false;
        }

        $followUp = $this->latestFollowUp?->followup_date;

        return $followUp
            ? Carbon::parse($followUp)->isBefore(now()->startOfDay())
            : false;
    }

    /* =====================================================
     | GLOBAL ACCESS SCOPE (FINAL)
     ===================================================== */

    protected static function booted()
    {
        static::addGlobalScope('access', function (Builder $query) {

            // Jangan ganggu artisan / seeder / queue
            if (app()->runningInConsole()) {
                return;
            }

            if (!auth()->check()) {
                return;
            }

            $user = auth()->user();

            /**
             * SUPERADMIN & SALES PUSAT
             * → full access
             */
            if ($user->isPusat()) {
                return;
            }

            /**
             * SALES AGENT
             * → hanya lead miliknya
             */
            if ($user->isAgent()) {
                $query->where('agent_id', $user->agent_id);
                return;
            }

            /**
             * ADMIN / OPERATOR CABANG
             * → by branch
             */
            if ($user->branch_id) {
                $query->where('branch_id', $user->branch_id);
            }
        });
    }
}

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Builder;

// // MODELS
// use App\Models\Agent;
// use App\Models\Branch;
// use App\Models\LeadSource;
// use App\Models\LeadActivity;
// use App\Models\LeadClosing;
// use Carbon\Carbon;

// class Lead extends Model
// {
//     protected $fillable = [
//         'nama','no_hp','email',
//         'sumber_id','channel',
//         'branch_id','agent_id',
//         'status','catatan',
//         'created_by',
//         'closed_at',
//         'dropped_at',
//         'drop_reason',
//     ];

//     /* ===============================
//      | RELATIONS
//      =============================== */

//     public function agent()
//     {
//         return $this->belongsTo(Agent::class);
//     }

//     public function branch()
//     {
//         return $this->belongsTo(Branch::class);
//     }

//     public function source()
//     {
//         return $this->belongsTo(LeadSource::class, 'sumber_id');
//     }
// // Lead.php
//     public function sumber()
//     {
//         return $this->belongsTo(LeadSource::class, 'sumber_id');
//     }

//     public function activities()
//     {
//         return $this->hasMany(LeadActivity::class)
//             ->orderByDesc('created_at');
//     }

//     public function latestFollowUp()
//     {
//         return $this->hasOne(LeadActivity::class)->latestOfMany();
//     }

//     public function closing()
//     {
//         return $this->hasOne(LeadClosing::class);
//     }

//     /* ===============================
//      | BUSINESS LOGIC
//      =============================== */


//     public function canSubmitClosing(): bool
//     {
//         return
//             $this->status === 'ACTIVE'
//             && !$this->closing
//             && auth()->user()->isAgent();
//     }

//     public function isLocked(): bool
//     {
//         return $this->status === 'CLOSED';
//     }

//     public function isOverdue(): bool
//     {
//         if ($this->status === 'CLOSED') {
//             return false;
//         }

//         $followUp = $this->latestFollowUp?->followup_date;

//         if (!$followUp) {
//             return false;
//         }

//         return Carbon::parse($followUp)->isBefore(now()->startOfDay());
//     }

//     public function jamaah()
//     {
//         return $this->hasOne(Jamaah::class, 'lead_id');
//     }
//     protected static function booted()
//     {
//         static::addGlobalScope('access', function (Builder $query) {

//             if (!auth()->check()) {
//                 return;
//             }

//             $user = auth()->user();

//             // SUPERADMIN → full access
//             if ($user->isPusat()) {
//                 return;
//             }

//             // AGENT (SALES) → hanya lead miliknya
//             if ($user->isAgent()) {
//                 $query->where('agent_id', $user->agent->id);
//                 return;
//             }

//             // ADMIN / OPERATOR CABANG → by branch
//             if ($user->branch_id) {
//                 $query->where('branch_id', $user->branch_id);
//             }
//         });
//     }

// }

// class Lead extends Model
// {
//     protected $table = 'leads';

//     protected $fillable = [
//         'nama',
//         'no_hp',
//         'email',

//         'sumber_id',
//         'channel',

//         'branch_id',
//         'agent_id',

//         'pipeline_id',
//         'status',
//         'catatan',

//         'created_by',
//         'closed_at',
//         'dropped_at',
//         'drop_reason',
//     ];

//     /* =====================================================
//      | GLOBAL SCOPE — ACCESS CONTROL
//      ===================================================== */
//     protected static function booted()
//     {
//         static::addGlobalScope('access', function (Builder $query) {

//             if (!auth()->check()) {
//                 return;
//             }

//             $user = auth()->user();

//             // 🔓 PUSAT
//             if ($user->isPusat()) {
//                 return;
//             }

//             // 🏢 CABANG
//             if ($user->isCabang()) {
//                 $query->where('branch_id', $user->branch_id);
//                 return;
//             }

//             // 👤 AGENT
//             if ($user->isAgent()) {
//                 $query->where('agent_id', optional($user->agent)->id);
//                 return;
//             }

//             // ❌ NO ACCESS
//             $query->whereRaw('1 = 0');
//         });
//     }

//     /* =====================================================
//      | RELATIONS
//      ===================================================== */

//     // Agent (sales)
//     public function agent()
//     {
//         return $this->belongsTo(Agent::class, 'agent_id');
//     }

//     // Branch
//     public function branch()
//     {
//         return $this->belongsTo(Branch::class, 'branch_id');
//     }

//     // User pembuat lead
//     public function createdBy()
//     {
//         return $this->belongsTo(User::class, 'created_by');
//     }

//     // Lead Source
//     public function source()
//     {
//         return $this->belongsTo(LeadSource::class, 'sumber_id');
//     }

//     // Alias legacy
//     public function sumber()
//     {
//         return $this->source();
//     }

//     // Pipeline
//     public function pipeline()
//     {
//         return $this->belongsTo(Pipeline::class, 'pipeline_id');
//     }

//     // Pipeline Logs
//     public function pipelineLogs()
//     {
//         return $this->hasMany(PipelineLog::class, 'lead_id')
//             ->orderByDesc('changed_at');
//     }

//     // Lead Activities
//     public function activities()
//     {
//         return $this->hasMany(LeadActivity::class, 'lead_id')
//             ->orderByDesc('created_at');
//     }

//     // ✅ Closing (SATU LEAD = SATU CLOSING)
//     public function closing()
//     {
//         return $this->hasOne(LeadClosing::class, 'lead_id');
//     }

//     /* =====================================================
//      | BUSINESS LOGIC
//      ===================================================== */

//     // Boleh ajukan closing?
//     public function canSubmitClosing(): bool
//     {
//         return
//             $this->status === 'WON'
//             && !$this->closing
//             && auth()->user()->isCabangOrAgent();
//     }

//     // Lead terkunci?
//     public function isLocked(): bool
//     {
//         return $this->status === 'CLOSED';
//     }

//     /* =====================================================
//      | SCOPES
//      ===================================================== */

//     public function scopeActive($query)
//     {
//         return $query->where('status', 'ACTIVE');
//     }

//     public function scopeClosed($query)
//     {
//         return $query->where('status', 'CLOSED');
//     }

//     public function scopeDropped($query)
//     {
//         return $query->where('status', 'DROPPED');
//     }

//     /* =========================================
//      | RELATION: FOLLOW UP TERAKHIR
//      ========================================= */
//     public function latestFollowUp()
//     {
//         return $this->hasOne(LeadActivity::class)->latestOfMany();
//     }

//     public function isOverdue(): bool
//     {
//         if ($this->status === 'CLOSED') {
//             return false;
//         }

//         $followUp = $this->latestFollowUp?->followup_date;

//         if (!$followUp) {
//             return false;
//         }

//         return Carbon::parse($followUp)->isBefore(now()->startOfDay());
//     }
    
// }

