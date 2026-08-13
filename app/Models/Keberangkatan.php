<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keberangkatan extends Model
{
    protected $table = 'keberangkatan';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_paket_master',
        'kode_keberangkatan',
        'tanggal_berangkat',
        'tanggal_pulang',
        'kuota',
        'seat_terisi',
        'jumlah_jamaah',
        'status',
    ];

    protected $casts = [
        'tanggal_berangkat' => 'date',
        'tanggal_pulang'    => 'date',
        'kuota'             => 'integer',
        'seat_terisi'       => 'integer',
        'jumlah_jamaah'     => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI PREMIUМ
    |--------------------------------------------------------------------------
    */

    /** Keberangkatan → Paket Master */
    public function paket()
    {
        return $this->belongsTo(\App\Models\PaketMaster::class, 'id_paket_master');
    }

    /** Alias untuk konsistensi */
    public function paketMaster()
    {
        return $this->belongsTo(\App\Models\PaketMaster::class, 'id_paket_master');
    }

    /** Keberangkatan → Jamaah */
    public function jamaah()
    {
        return $this->hasMany(\App\Models\Jamaah::class, 'id_keberangkatan');
    }
    // App\Models\Keberangkatan.php
    public function jamaahAll()
    {
        return $this->hasMany(\App\Models\Jamaah::class, 'id_keberangkatan')
            ->withoutGlobalScopes();
    }

    /** Keberangkatan → Invoice (melalui jamaah) */
    public function invoices()
    {
        return $this->hasManyThrough(
            \App\Models\Invoices::class,
            \App\Models\Jamaah::class,
            'id_keberangkatan',   // FK di jamaah
            'jamaah_id',          // FK di invoices
            'id',                 // PK di keberangkatan
            'id'                  // PK di jamaah
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS (AUTO)
    |--------------------------------------------------------------------------
    */

    /** Nama Paket */
    public function getNamaPaketAttribute()
    {
        return $this->paket?->nama_paket ?? '-';
    }

    /** Jumlah jamaah aktif */
    public function getTotalJamaahActiveAttribute()
    {
        return $this->jamaah()->count();
    }

    /** Sisa seat tersedia */
    public function getSisaSeatAttribute()
    {
        return max(($this->kuota ?? 0) - ($this->seat_terisi ?? 0), 0);
    }

    /** Status readable */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'aktif'     => 'Aktif',
            'berangkat' => 'Sudah Berangkat',
            'selesai'   => 'Selesai',
            default     => 'Unknown',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES FILTER PREМIUМ
    |--------------------------------------------------------------------------
    */

    public function scopeAktif($q)
    {
        return $q->where('status', 'aktif');
    }

    public function scopeSelesai($q)
    {
        return $q->where('status', 'selesai');
    }

    public function scopeByPaket($q, $paketId)
    {
        return $q->where('id_paket_master', $paketId);
    }

    public function scopeByTanggal($q, $tanggal)
    {
        return $q->whereDate('tanggal_berangkat', $tanggal);
    }

    public function scopeDalamRentang($q, $awal, $akhir)
    {
        return $q->whereBetween('tanggal_berangkat', [$awal, $akhir]);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER FUNGSIONAL PREMIUM
    |--------------------------------------------------------------------------
    */

    /** Tambah seat terisi */
    public function tambahSeat($jumlah = 1)
    {
        $this->seat_terisi = ($this->seat_terisi ?? 0) + $jumlah;
        $this->save();
    }

    /** Kurangi seat (jika jamaah batal) */
    public function kurangiSeat($jumlah = 1)
    {
        $this->seat_terisi = max(($this->seat_terisi ?? 0) - $jumlah, 0);
        $this->save();
    }

    /** Hitung ulang jumlah_jamaah (auto sync) */
    public function syncJumlahJamaah()
    {
        $this->jumlah_jamaah = $this->jamaah()->count();
        $this->save();
    }
    // App\Models\Keberangkatan.php
    public function tripExpenses()
    {
        return $this->hasMany(
            \App\Models\TripExpenses::class,
            'paket_id',
            'id_paket_master'
        );
    }

}
