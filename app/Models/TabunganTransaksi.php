<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TabunganTransaksi extends Model
{
    protected $table = 'tabungan_transaksi';

    protected $fillable = [
        'tabungan_id',
        'jenis',
        'amount',
        'saldo_setelah',
        'reference_type',
        'reference_id',
        'keterangan',
        'created_by',
        'created_at',
    ];

    public $timestamps = false;

    protected $casts = [
        'amount'         => 'integer',
        'saldo_setelah'  => 'integer',
        'created_at'     => 'datetime',
    ];

    /**
     * =========================
     * RELATIONS
     * =========================
     */

    public function tabungan()
    {
        return $this->belongsTo(TabunganUmrah::class, 'tabungan_id');
    }

    /**
     * =========================
     * SCOPES
     * =========================
     */

    public function scopeTopup($query)
    {
        return $query->where('jenis', 'TOPUP');
    }

    public function scopeDebit($query)
    {
        return $query->whereIn('jenis', ['TARIK', 'TRANSFER_INVOICE']);
    }

    // RELASI KE BUKTI SETORAN
    public function buktiSetoran()
    {
        return $this->hasOne(BuktiSetoran::class, 'tabungan_transaksi_id');
    }

    // RELASI KE TOPUP
    public function topup()
    {
        return $this->belongsTo(TabunganTopup::class, 'reference_id')
                    ->where('reference_type', 'TOPUP');
    }

    // OPSIONAL
    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class, 'jamaah_id');
    }

}
