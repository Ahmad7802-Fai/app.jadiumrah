<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuktiSetoran extends Model
{
    protected $table = 'bukti_setoran';
    public $timestamps = false;

    protected $fillable = [
        'nomor_bukti',
        'tabungan_transaksi_id',
        'jamaah_id',
        'tabungan_id',
        'nominal',
        'tanggal_setoran',
        'approved_by',
        'approved_at',
        'hash',
        'qr_path',
    ];

    /* ======================
     | RELATIONS
     ====================== */

   // SEBELUM
    public function transaksi()
    {
        return $this->belongsTo(TabunganTransaksi::class, 'tabungan_transaksi_id');
    }

    // SESUDAH
    public function tabunganTransaksi()
    {
        return $this->belongsTo(TabunganTransaksi::class, 'tabungan_transaksi_id');
    }


    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class, 'jamaah_id');
    }

    public function tabungan()
    {
        return $this->belongsTo(TabunganUmrah::class, 'tabungan_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
