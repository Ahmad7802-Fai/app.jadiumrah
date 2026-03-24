<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomMember extends Model
{
    protected $fillable = [
        'room_id',
        'jamaah_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class);
    }
}