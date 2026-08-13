<?php

namespace App\Http\Resources\Api\V1\User;

use Illuminate\Http\Resources\Json\JsonResource;

class JamaahResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id ?? null,
            'jamaah_code' => $this->jamaah_code ?? null,
            'nama_lengkap' => $this->nama_lengkap ?? null,
            'phone' => $this->phone ?? null,
        ];
    }
}