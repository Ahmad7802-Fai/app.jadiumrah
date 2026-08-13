<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Support\AccessScope;
use App\Models\JamaahUser;
use Illuminate\Database\Eloquent\Relations\HasOne;
class Jamaah extends Model
{
    protected $table = 'jamaah';

    /* ============================================================
     | GLOBAL ACCESS SCOPE
     | - Auto filter berdasarkan role
     | - Satu sumber kebenaran: access.context
     ============================================================ */
    protected static function booted()
    {
        if (!auth()->guard('jamaah')->check()) {
            static::addGlobalScope('access', function (Builder $query) {
                AccessScope::apply($query);
            });
        }

        // 🔒 safety default
        static::creating(function ($jamaah) {
            if ($jamaah->agent_id && !$jamaah->mode) {
                $jamaah->mode = 'affiliate';
            }

            if (!$jamaah->source) {
                $jamaah->source = 'website';
            }
        });
    }


    /* ============================================================
     | MASS ASSIGNMENT
     ============================================================ */
    protected $fillable = [
        // CONTEXT
        'branch_id',
        'agent_id',
        'lead_id', // 🔥 TAMBAH
        // 🔥 TAMBAHKAN INI
        'mode',
        'source',
        
        // RELASI
        'id_keberangkatan',
        'id_paket_master',

        // PAKET & HARGA
        'nama_paket',
        'paket',
        'harga_default',
        'harga_disepakati',
        'diskon',
        'deposit',
        'sisa',
        'tipe_jamaah',
        // IDENTITAS
        'no_id',
        'nama_lengkap',
        'nama_passport',
        'nama_ayah',
        'nik',
        'no_hp',

        // LAHIR
        'tempat_lahir',
        'tanggal_lahir',
        'usia',
        'kelompok_usia',

        // STATUS PERSONAL
        'status_pernikahan',
        'jenis_kelamin',

        // MAHRAM
        'nama_mahram',
        'status_mahram',

        // SCREENING (🔥 INI YANG KURANG)
        'pernah_umroh',
        'pernah_haji',
        'merokok',
        'penyakit_khusus',
        'nama_penyakit',
        'kursi_roda',
        'butuh_penanganan_khusus',

        // TAMBAHAN
        'foto',
        'tipe_kamar',
        'keterangan',

        // APPROVAL
        'status',
        'approved_by',
        'approved_at',
        'rejected_reason',
    ];


    /* ============================================================
     | CASTS
     ============================================================ */
    protected $casts = [
        'tanggal_lahir'    => 'date',
        'harga_default'    => 'integer',
        'harga_disepakati' => 'integer',
        'diskon'           => 'integer',
        'usia'             => 'integer',
        'approved_at'      => 'datetime',
    ];
    public function jamaahUser()
    {
        return $this->hasOne(JamaahUser::class, 'jamaah_id');
    }
    /* ============================================================
     | RELATIONS
     ============================================================ */

    public function keberangkatan()
    {
        return $this->belongsTo(Keberangkatan::class, 'id_keberangkatan');
    }

    public function paketMaster()
    {
        return $this->belongsTo(PaketMaster::class, 'id_paket_master');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoices::class, 'jamaah_id')->latest();
    }

    public function payments()
    {
        return $this->hasMany(Payments::class, 'jamaah_id')
            ->where('is_deleted', 0);
    }

    public function logs()
    {
        return $this->hasMany(JamaahLogs::class, 'jamaah_id')->latest();
    }

    /* ============================================================
     | ACCESSORS
     ============================================================ */

    /**
     * Harga akhir jamaah (disepakati > default)
     */
    public function getHargaAkhirAttribute(): int
    {
        return (int) (
            $this->harga_disepakati
            ?? $this->harga_default
            ?? 0
        );
    }

    /**
     * Sisa tagihan jamaah
     */
    public function getSisaTagihanAttribute(): int
    {
        $invoice = $this->invoices()->first();

        return $invoice
            ? (int) $invoice->sisa_tagihan
            : $this->harga_akhir;
    }

    /* ============================================================
     | QUERY SCOPES (NON-AKSES)
     ============================================================ */

    public function scopeLunas($query)
    {
        return $query->where('sisa', '<=', 0);
    }

    public function scopeBelumLunas($query)
    {
        return $query->where('sisa', '>', 0);
    }
    public function paket()
    {
        return $this->paketMaster();
    }
    public function tabungan(): HasOne
    {
        return $this->hasOne(
            TabunganUmrah::class,
            'jamaah_id'
        );
    }

    // App\Models\Jamaah.php
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    private function findJamaahOrFail(int $id): Jamaah
    {
        return Jamaah::withoutGlobalScopes()->findOrFail($id);
    }

}
