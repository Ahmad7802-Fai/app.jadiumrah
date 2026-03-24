<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SavingGoalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'goal_name' => $this->goal_name,

            'target_amount' => $this->target_amount,

            'target_date' => $this->target_date,

        ];
    }
}