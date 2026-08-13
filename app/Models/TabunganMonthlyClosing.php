<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TabunganMonthlyClosing extends Model
{
    protected $table = 'tabungan_monthly_closings';

    protected $fillable = [
        'bulan',
        'tahun',
        'total_saldo_awal',
        'total_topup',
        'total_debit',
        'total_saldo_akhir',
        'closed_at',
        'closed_by',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'closed_by');
    }
}
