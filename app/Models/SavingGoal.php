<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingGoal extends Model
{

    protected $table = 'saving_goals';

    protected $fillable = [

        'saving_account_id',

        'goal_name',

        'target_amount',

        'target_date'

    ];

    protected $casts = [

        'target_amount' => 'decimal:2',

        'target_date' => 'date'

    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function account()
    {
        return $this->belongsTo(SavingAccount::class,'saving_account_id');
    }

}