<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * JamaahPublic
 *
 * - Dipakai KHUSUS untuk jamaah yang login sendiri
 * - TANPA GlobalScope AccessScope
 * - AMAN untuk Tabungan, Dashboard, Profile Jamaah
 */
class JamaahPublic extends Model
{
    protected $table = 'jamaah';

    protected $primaryKey = 'id';

    public $incrementing = true;

    public $timestamps = true;

    /**
     * =========================
     * MASS ASSIGNMENT
     * =========================
     */
    protected $fillable = [
        'branch_id',
        'agent_id',
        'id_keberangkatan',
        'id_paket_master',
        'nama_paket',
        'paket',
        'harga_default',
        'harga_disepakati',
        'diskon',
        'deposit',
        'sisa',
        'no_id',
        'nama_lengkap',
        'nama_passport',
        'nama_ayah',
        'nik',
        'no_hp',
        'tempat_lahir',
        'tanggal_lahir',
        'usia',
        'kelompok_usia',
        'status_pernikahan',
        'jenis_kelamin',
        'nama_mahram',
        'status_mahram',
        'pernah_umroh',
        'pernah_haji',
        'merokok',
        'penyakit_khusus',
        'nama_penyakit',
        'kursi_roda',
        'butuh_penanganan_khusus',
        'foto',
        'tipe_kamar',
        'keterangan',
        'status',
        'approved_by',
        'approved_at',
        'rejected_reason',
    ];

    /**
     * =========================
     * CASTS
     * =========================
     */
    protected $casts = [
        'tanggal_lahir' => 'date',
        'approved_at'   => 'datetime',
        'harga_default' => 'integer',
        'harga_disepakati' => 'integer',
        'diskon' => 'integer',
        'usia' => 'integer',
    ];

    /**
     * =========================
     * RELATIONS (PUBLIC ONLY)
     * =========================
     */
    public function tabungan(): HasOne
    {
        return $this->hasOne(TabunganUmrah::class, 'jamaah_id');
    }

    public function jamaahUser(): HasOne
    {
        return $this->hasOne(JamaahUser::class, 'jamaah_id');
    }

    // App\Models\JamaahPublic.php
    public function keberangkatan()
    {
        return $this->belongsTo(
            Keberangkatan::class,
            'id_keberangkatan',
            'id'
        );
    }

    public function paket()
    {
        return $this->belongsTo(
            PaketMaster::class,
            'id_paket_master',
            'id'
        );
    }

    public function getStatusKeberangkatanAttribute()
    {
        return match (optional($this->keberangkatan)->status) {
            'Aktif'   => 'persiapan',
            'Selesai' => 'berangkat',
            'Batal'   => 'menunggu',
            default   => 'menunggu',
        };
    }
    public function getPaketAktifAttribute()
    {
        // 1️⃣ Prioritas: paket dari keberangkatan
        if ($this->keberangkatan && $this->keberangkatan->paket) {
            return $this->keberangkatan->paket;
        }

        // 2️⃣ Fallback: paket langsung di jamaah (legacy)
        return $this->paket;
    }

}
