<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingTransaction extends Model
{

    protected $fillable = [
        'saving_account_id',
        'type',
        'amount',
        'reference',
        'note'
    ];

    public function account()
    {
        return $this->belongsTo(SavingAccount::class,'saving_account_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

}