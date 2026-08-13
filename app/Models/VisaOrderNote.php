<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisaOrderNote extends Model
{
    use HasFactory;

    public const TYPE_INTERNAL = 'internal';
    public const TYPE_CUSTOMER = 'customer';
    public const TYPE_SYSTEM = 'system';

    protected $fillable = [
        'visa_order_id',
        'user_id',
        'note_type',
        'note',
    ];

    protected $casts = [
        'visa_order_id' => 'integer',
        'user_id' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function order()
    {
        return $this->belongsTo(VisaOrder::class, 'visa_order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}