<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SavingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'user_id' => $this->user_id,

            'jamaah_id' => $this->jamaah_id,

            'account_number' => $this->account_number,

            'balance' => $this->balance,

            'status' => $this->status,

            'created_at' => $this->created_at?->toDateTimeString(),

            /*
            |--------------------------------------------------------------------------
            | RELATIONS
            |--------------------------------------------------------------------------
            */

            'transactions' => SavingTransactionResource::collection(
                $this->whenLoaded('transactions')
            ),

            'goals' => SavingGoalResource::collection(
                $this->whenLoaded('goals')
            ),

        ];
    }
}