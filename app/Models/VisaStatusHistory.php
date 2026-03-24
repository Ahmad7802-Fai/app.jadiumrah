<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisaStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'visa_order_id',
        'from_status',
        'to_status',
        'description',
        'changed_by',
        'changed_at',
    ];

    protected $casts = [
        'visa_order_id' => 'integer',
        'changed_by' => 'integer',
        'changed_at' => 'datetime',
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

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}