<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TabunganUmrah extends Model
{
    protected $table = 'tabungan_umrah';

    protected $fillable = [
        'jamaah_id',
        'nomor_tabungan',
        'nama_tabungan',
        'target_nominal',
        'saldo',
        'status',
    ];

    protected $casts = [
        'saldo'          => 'integer',
        'target_nominal' => 'integer',
    ];

    /* =========================
     * RELATIONS
     * ========================= */

    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class, 'jamaah_id');
    }

    public function transaksi()
    {
        return $this->hasMany(TabunganTransaksi::class, 'tabungan_id');
    }

    public function topups()
    {
        return $this->hasMany(TabunganTopup::class, 'tabungan_id');
    }

    /* =========================
     * SCOPES
     * ========================= */

    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    /* =========================
     * HELPERS
     * ========================= */

    public function isActive(): bool
    {
        return $this->status === 'ACTIVE';
    }
}

