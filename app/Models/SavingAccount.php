<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingAccount extends Model
{

    protected $fillable = [
        'user_id',
        'jamaah_id',
        'account_number',
        'balance',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class);
    }

    public function transactions()
    {
        return $this->hasMany(SavingTransaction::class);
    }

    public function goals()
{
    return $this->hasMany(SavingGoal::class);
}

}