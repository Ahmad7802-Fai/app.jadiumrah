<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Closings extends Model
{
    use HasFactory;

    protected $table = 'closings';
    protected $primaryKey = 'id';

    public $timestamps = false; // FIX ERROR updated_at & created_at

    protected $fillable = [
        'lead_id',
        'tanggal',
        'nominal_dp',
        'paket_umroh',
        'user_id',
        'catatan',
    ];

    protected $casts = [
        'tanggal'    => 'datetime',
        'nominal_dp' => 'integer',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function sales()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}

