<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaketUmrah extends Model
{
    protected $table = 'paket_umrah';

    protected $fillable = [
        'title',
        'slug',
        'seo_title',
        'tglberangkat',
        'pesawat',
        'flight',
        'durasi',
        'seat',
        'hotmekkah',
        'rathotmekkah',
        'hotmadinah',
        'rathotmadinah',
        'quad',
        'triple',
        'double',
        'itin',
        'photo',
        'thaif',
        'dubai',
        'kereta',
        'deskripsi',
        'status',
        'is_active',
    ];

    // auto slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($paket) {
            if (empty($paket->slug)) {
                $paket->slug = Str::slug($paket->title);
            }
        });
    }

}
