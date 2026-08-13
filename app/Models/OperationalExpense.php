<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OperationalExpense extends Model
{
    use HasFactory;

    protected $table = 'operational_expenses';

    // Karena tabel TIDAK punya updated_at
    public $timestamps = false;

    // Cast agar otomatis jadi Carbon object
    protected $casts = [
        'tanggal'     => 'date',
        'created_at'  => 'datetime',
    ];

    protected $fillable = [
        'kategori',
        'deskripsi',
        'jumlah',
        'tanggal',
        'bukti',
        'dibuat_oleh',
        'created_at'
    ];

    // Relasi user
    public function user()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
