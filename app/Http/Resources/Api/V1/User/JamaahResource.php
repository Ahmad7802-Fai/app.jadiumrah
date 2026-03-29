<?php

namespace App\Http\Resources\Api\V1\User;

use Illuminate\Http\Resources\Json\JsonResource;

class JamaahResource extends JsonResource
{
    public function toArray($request): array
    {
        if (!$this) return [];

        return [
            'id' => $this->id,
            'jamaah_code' => $this->jamaah_code,
            'nama_lengkap' => $this->nama_lengkap,
            'phone' => $this->phone,
        ];
    }
}