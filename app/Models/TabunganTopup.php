<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TabunganTopup extends Model
{
    protected $table = 'tabungan_topups';

   protected $fillable = [
        'tabungan_id',
        'jamaah_id',
        'amount',
        'transfer_date',
        'bank_sender',
        'bank_receiver',
        'proof_file',
        'status',
        'admin_note',
        'verified_by',
        'verified_at',
        'wa_token',
        'wa_verified_at',
    ];

    public $timestamps = false;

    protected $casts = [
        'amount'        => 'integer',
        'transfer_date' => 'date',
        'verified_at'   => 'datetime',
        'created_at'    => 'datetime',
    ];

    public function tabungan()
    {
        return $this->belongsTo(TabunganUmrah::class, 'tabungan_id');
    }

    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class, 'jamaah_id');
    }

    public function transaksi()
    {
        return $this->hasOne(
            TabunganTransaksi::class,
            'reference_id',
            'id'
        )->where('reference_type', 'TOPUP');
    }

    public function buktiSetoran()
    {
        return $this->hasOneThrough(
            BuktiSetoran::class,
            TabunganTransaksi::class,
            'reference_id',              // FK di transaksi
            'tabungan_transaksi_id',     // FK di bukti
            'id',                        // PK topup
            'id'                         // PK transaksi
        )->where('reference_type', 'TOPUP');
    }

}
