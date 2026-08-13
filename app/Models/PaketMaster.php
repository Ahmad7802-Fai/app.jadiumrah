<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketMaster extends Model
{
    protected $table = 'paket_master';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_paket',
        'pesawat',
        'hotel_mekkah',
        'hotel_madinah',
        'harga_quad',
        'harga_triple',
        'harga_double',
        'diskon_default',
        'is_active',
    ];

    protected $casts = [
        'harga_quad'    => 'integer',
        'harga_triple'  => 'integer',
        'harga_double'  => 'integer',
        'diskon_default'=> 'integer',
        'is_active'     => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI PREMIUM
    |--------------------------------------------------------------------------
    */

    /** Paket → Banyak Keberangkatan */
    public function keberangkatan()
    {
        return $this->hasMany(\App\Models\Keberangkatan::class, 'id_paket_master');
    }

    /** Paket → Banyak Jamaah */
    public function jamaah()
    {
        return $this->hasMany(\App\Models\Jamaah::class, 'id_paket_master');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS (Getter Otomatis)
    |--------------------------------------------------------------------------
    */

    /** Nama hotel gabungan */
    public function getHotelFullAttribute()
    {
        return $this->hotel_mekkah . ' & ' . $this->hotel_madinah;
    }

    /** Harga Quad Format */
    public function getHargaQuadFormatAttribute()
    {
        return 'Rp ' . number_format($this->harga_quad, 0, ',', '.');
    }

    /** Harga Triple Format */
    public function getHargaTripleFormatAttribute()
    {
        return 'Rp ' . number_format($this->harga_triple, 0, ',', '.');
    }

    /** Harga Double Format */
    public function getHargaDoubleFormatAttribute()
    {
        return 'Rp ' . number_format($this->harga_double, 0, ',', '.');
    }

    /** Harga Default (untuk invoice atau jamaah tanpa tipe kamar) */
    public function getHargaDefaultAttribute()
    {
        return $this->harga_triple; // default harga umum
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES (Filter cepat)
    |--------------------------------------------------------------------------
    */

    public function scopeAktif($q)
    {
        return $q->where('is_active', 1);
    }

    public function scopeNonAktif($q)
    {
        return $q->where('is_active', 0);
    }

    public function scopeNama($q, $nama)
    {
        return $q->where('nama_paket', 'like', "%$nama%");
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER FUNGSIONAL PREMIUM
    |--------------------------------------------------------------------------
    */

    /**
     * Hitung harga paket berdasarkan tipe kamar jamaah.
     * Digunakan oleh auto harga jamaah dan invoice.
     */
    public function hargaByKamar($tipe)
    {
        return match ($tipe) {
            'quad'   => $this->harga_quad,
            'triple' => $this->harga_triple,
            'double' => $this->harga_double,
            default  => $this->harga_triple, // fallback normal
        };
    }

    /**
     * Hitung harga final dengan diskon default
     */
    public function hargaDenganDiskon($tipe)
    {
        $hargaKamar = $this->hargaByKamar($tipe);
        return max($hargaKamar - ($this->diskon_default ?? 0), 0);
    }
}
