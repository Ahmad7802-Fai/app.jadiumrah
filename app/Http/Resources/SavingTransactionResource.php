<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SavingTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'type' => $this->type,

            'amount' => $this->amount,

            'reference' => $this->reference,

            'note' => $this->note,

            'created_at' => $this->created_at?->toDateTimeString(),

        ];
    }
}