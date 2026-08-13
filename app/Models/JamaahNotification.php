<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JamaahNotification extends Model
{
    protected $table = 'jamaah_notifications';

    protected $fillable = [
        'jamaah_id',
        'title',
        'message',
        'is_read',
        'created_at',
    ];

    protected $attributes = [
        'is_read' => 0,
    ];

    protected $casts = [
        'is_read'    => 'boolean',
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class);
    }

    public function markAsRead()
    {
        $this->update(['is_read' => 1]);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', 0);
    }
}
