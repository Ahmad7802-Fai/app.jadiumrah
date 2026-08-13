<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    use HasFactory;

    protected $table = 'invoices';
    protected $primaryKey = 'id';

    protected $fillable = [
        'jamaah_id',
        'nomor_invoice',
        'tanggal',
        'total_tagihan',
        'total_terbayar',
        'sisa_tagihan',
        'status',
        'paket_id',
        'kamar',
    ];

    protected $casts = [
        'tanggal'        => 'date',
        'total_tagihan'  => 'integer',
        'total_terbayar' => 'integer',
        'sisa_tagihan'   => 'integer',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    // Invoice → Jamaah
    public function jamaah()
    {
        return $this->belongsTo(\App\Models\Jamaah::class, 'jamaah_id')
            ->withoutGlobalScopes();
    }


    // Invoice → Paket
    public function paket()
    {
        return $this->belongsTo(\App\Models\PaketMaster::class, 'paket_id');
    }

    // Invoice → Payments
    public function payments()
    {
        return $this->hasMany(\App\Models\Payments::class, 'invoice_id')
                    ->orderBy('tanggal_bayar', 'asc');
    }

    // Invoice → Installments (Opsional jika ada fitur cicilan)
    public function installments()
    {
        return $this->hasMany(\App\Models\InvoiceInstallments::class, 'invoice_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS (AUTO HITUNG)
    |--------------------------------------------------------------------------
    */

    // Total pembayaran dari tabel payments
    public function getTotalPembayaranAttribute()
    {
        return $this->payments()->valid()->sum('jumlah');
    }

    // Hitung otomatis sisa tagihan
    public function getSisaTagihanAutoAttribute()
    {
        return $this->total_tagihan - $this->total_pembayaran;
    }

    // Label status
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'belum_lunas'        => 'Belum Lunas',
            'menunggu_validasi'  => 'Menunggu Validasi',
            'cicilan'            => 'Cicilan',
            'lunas'              => 'Lunas',
            default              => '-'
        };
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS / AUTO UPDATE FIELD
    |--------------------------------------------------------------------------
    */

    public function updateSummary()
    {
        $totalBayar = $this->payments()->valid()->sum('jumlah');

        $this->total_terbayar = $totalBayar;
        $this->sisa_tagihan   = max($this->total_tagihan - $totalBayar, 0);

        if ($this->sisa_tagihan <= 0) {
            $this->status = 'lunas';
        } elseif ($totalBayar > 0) {
            $this->status = 'cicilan';
        } else {
            $this->status = 'belum_lunas';
        }

        $this->save();
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATOR NOMOR INVOICE
    |--------------------------------------------------------------------------
    */

    public static function generateNomorInvoice()
    {
        $tahun = date('Y');

        // Cek nomor invoice terakhir di tahun ini
        $last = self::whereYear('created_at', $tahun)
                    ->orderBy('id', 'desc')
                    ->first();

        $nextNumber = $last ? intval(substr($last->nomor_invoice, -4)) + 1 : 1;

        return 'INV-' . $tahun . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeBelumLunas($q)
    {
        return $q->where('status', 'belum_lunas');
    }

    public function scopeCicilan($q)
    {
        return $q->where('status', 'cicilan');
    }

    public function scopeLunas($q)
    {
        return $q->where('status', 'lunas');
    }
}
