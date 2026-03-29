<?php

namespace App\Http\Resources\Api\V1\User;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\UserRoleResolver;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        $role = UserRoleResolver::resolve($this);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,

            'role' => $role,
            'roles' => $this->getRoleNames()->values(),

            'branch' => $this->branch?->only(['id','name']),

            'agent' => $role === 'agent'
                ? new AgentResource($this->agentProfile)
                : null,

            'jamaah' => $role === 'jamaah'
                ? new JamaahResource($this->jamaahProfile)
                : null,
        ];
    }
}