<?php

namespace App\Http\Resources\Api\V1\User;

use Illuminate\Http\Resources\Json\JsonResource;

class AgentResource extends JsonResource
{
    public function toArray($request): array
    {
        if (!$this) return [];

        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'kode_agent' => $this->kode_agent,
            'phone' => $this->phone,
        ];
    }
}