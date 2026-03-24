<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'departure_id',
        'hotel_name',
        'city',
        'room_number',
        'gender',
        'capacity',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function departure()
    {
        return $this->belongsTo(PaketDeparture::class, 'departure_id');
    }

    public function members()
    {
        return $this->hasMany(RoomMember::class);
    }

    public function jamaahs()
    {
        return $this->belongsToMany(
            Jamaah::class,
            'room_members'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isFull(): bool
    {
        return $this->members()->count() >= $this->capacity;
    }

    public function remainingSeat(): int
    {
        return $this->capacity - $this->members()->count();
    }
}