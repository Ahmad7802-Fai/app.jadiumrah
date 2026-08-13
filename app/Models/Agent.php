<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class Agent extends Model
{
    protected $table = 'agents';

    protected $fillable = [
        'user_id',
        'branch_id',
        'slug',
        'nama',
        'kode_agent',
        'phone',

        // BANK
        'bank_name',
        'bank_account_number',
        'bank_account_name',

        'komisi_persen',
        'komisi_manual',
        'komisi_affiliate',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /* =====================================================
     | GLOBAL ACCESS SCOPE (FINAL)
     ===================================================== */
    protected static function booted()
    {
        // 🔒 VISIBILITY
        static::addGlobalScope('access', function (Builder $query) {

            if (! auth()->check()) {
                $query->whereRaw('1 = 0');
                return;
            }

            $user = auth()->user();

            // SUPERADMIN & OPERATOR → FULL
            if (in_array($user->role, ['SUPERADMIN', 'OPERATOR'])) {
                return;
            }

            // ADMIN → AGENT CABANG
            if ($user->role === 'ADMIN') {
                $query->where('branch_id', $user->branch_id);
                return;
            }

            // SALES → HANYA AGENT DIRINYA
            if ($user->isAgent()) {
                $query->where('id', $user->agent_id);
                return;
            }

            // ROLE LAIN → NO ACCESS
            $query->whereRaw('1 = 0');
        });

        // 🔁 SINKRON KOMISI
        static::saving(function ($agent) {
            if ($agent->isDirty('komisi_persen')) {
                $agent->komisi_manual    = $agent->komisi_persen;
                $agent->komisi_affiliate = $agent->komisi_persen;
            }
        });
    }

    /* =====================================================
     | RELATIONS
     ===================================================== */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function jamaah()
    {
        return $this->hasMany(Jamaah::class, 'agent_id');
    }

    /* =====================================================
     | BUSINESS METRICS
     ===================================================== */

    /** Total Jamaah */
    public function totalJamaah(): int
    {
        return $this->jamaah()->count();
    }

    /** Total Omset (ALL TIME) */
    public function totalOmset(): int
    {
        return DB::table('payments')
            ->join('jamaah', 'jamaah.id', '=', 'payments.jamaah_id')
            ->where('jamaah.agent_id', $this->id)
            ->where('payments.status', 'valid')
            ->where('payments.is_deleted', 0)
            ->where('payments.is_correction', 0)
            ->sum('payments.jumlah');
    }

    /** Omset Per Bulan */
    public function omsetPerBulan(int $year): array
    {
        return DB::table('payments')
            ->join('jamaah', 'jamaah.id', '=', 'payments.jamaah_id')
            ->where('jamaah.agent_id', $this->id)
            ->where('payments.status', 'valid')
            ->where('payments.is_deleted', 0)
            ->where('payments.is_correction', 0)
            ->whereYear('payments.tanggal_bayar', $year)
            ->selectRaw('
                MONTH(payments.tanggal_bayar) as month,
                SUM(payments.jumlah) as total
            ')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();
    }

    public function omsetBulan(int $year, int $month): int
    {
        return DB::table('payments')
            ->join('jamaah', 'jamaah.id', '=', 'payments.jamaah_id')
            ->where('jamaah.agent_id', $this->id)
            ->where('payments.status', 'valid')
            ->where('payments.is_deleted', 0)
            ->where('payments.is_correction', 0)
            ->whereYear('payments.tanggal_bayar', $year)
            ->whereMonth('payments.tanggal_bayar', $month)
            ->sum('payments.jumlah');
    }

    public function jamaahBulan(int $year, int $month): int
    {
        return $this->jamaah()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();
    }


    public function leads()
    {
        return $this->hasMany(Lead::class, 'agent_id');
    }

    /* =====================================================
     | BUSINESS HELPERS
     ===================================================== */

    public function hasValidBankAccount(): bool
    {
        return ! empty($this->bank_name)
            && ! empty($this->bank_account_number)
            && ! empty($this->bank_account_name);
    }

    public function getReferralKeyAttribute(): string
    {
        return $this->slug ?: $this->kode_agent;
    }
}

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
// use App\Support\AccessScope;
// use Illuminate\Database\Eloquent\Builder;
// class Agent extends Model
// {
//     protected $table = 'agents';

//     protected $fillable = [
//         'user_id',
//         'branch_id',
//         'slug',
//         'nama',
//         'kode_agent',
//         'phone',

//         // 🔥 BANK
//         'bank_name',
//         'bank_account_number',
//         'bank_account_name',

//         'komisi_persen',
//         'komisi_manual',
//         'komisi_affiliate',
//         'is_active',
//     ];


//     protected $casts = [
//         'is_active' => 'boolean',
//     ];
//     protected static function booted()
//     {
//         static::saving(function ($agent) {
//             if ($agent->isDirty('komisi_persen')) {
//                 $agent->komisi_manual    = $agent->komisi_persen;
//                 $agent->komisi_affiliate = $agent->komisi_persen;
//             }
//         });
//     }

//     public function user()
//     {
//         return $this->belongsTo(User::class);
//     }

//     public function jamaah()
//     {
//         return $this->hasMany(Jamaah::class, 'agent_id');
//     }
//     public function leads()
//     {
//         return $this->hasMany(Lead::class, 'agent_id');
//     }

//     public function branch()
//     {
//         return $this->belongsTo(Branch::class, 'branch_id');
//     }

//     public function scopeByAccess(Builder $query): Builder
//     {
//         $user = auth()->user();

//         if (! $user) {
//             return $query->whereRaw('1 = 0');
//         }

//         // SUPERADMIN & OPERATOR → full access
//         if (in_array($user->role, ['SUPERADMIN', 'OPERATOR'])) {
//             return $query;
//         }

//         // ADMIN → hanya agent cabangnya
//         if ($user->role === 'ADMIN') {
//             return $query->where('branch_id', $user->branch_id);
//         }

//         // SALES & lainnya → tidak boleh lihat agent
//         return $query->whereRaw('1 = 0');
//     }

//     public function hasValidBankAccount(): bool
//     {
//         return ! empty($this->bank_name)
//             && ! empty($this->bank_account_number)
//             && ! empty($this->bank_account_name);
//     }

//     public function getReferralKeyAttribute()
//     {
//         return $this->slug ?: $this->kode_agent;
//     }

// }
