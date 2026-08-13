<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingExpenses extends Model
{
    use HasFactory;
    public $timestamps = false; // ⬅️ TAMBAHKAN INI
    protected $table = 'marketing_expenses';

    protected $fillable = [
        'sumber_id',
        'nama_campaign',
        'platform',
        'biaya',
        'tanggal',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'biaya'   => 'integer',
        'tanggal' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function sumber()
    {
        return $this->belongsTo(LeadSource::class, 'sumber_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getBiayaFormattedAttribute()
    {
        return number_format($this->biaya, 0, ',', '.');
    }

    public function getTanggalIndoAttribute()
    {
        return $this->tanggal
            ? $this->tanggal->translatedFormat('d M Y')
            : '-';
    }

    public function getLabelAttribute()
    {
        return trim(
            ($this->nama_campaign ? $this->nama_campaign.' – ' : '') .
            ($this->sumber->nama_sumber ?? '')
        );
    }

    public const PLATFORMS = [
        'meta_ads'   => 'Meta Ads',
        'tiktok_ads' => 'Tiktok Ads',
        'google_ads' => 'Google Ads',
        'offline'    => 'Offline',
    ];
}

