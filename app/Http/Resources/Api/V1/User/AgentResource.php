<?php

namespace App\Http\Resources\Api\V1\User;

use Illuminate\Http\Resources\Json\JsonResource;

class AgentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id ?? null,
            'nama' => $this->nama ?? null,
            'kode_agent' => $this->kode_agent ?? null,
            'phone' => $this->phone ?? null,
        ];
    }
}