<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';

    protected $fillable = [
        'tipe',
        'nama',
        'pic',
        'alamat',
        'telepon',
        'email',
        'npwp',
    ];

    public function transaksi()
    {
        return $this->hasMany(LayananTransaksi::class, 'id_client');
    }

}
