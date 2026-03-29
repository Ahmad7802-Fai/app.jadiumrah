<?php

namespace App\Http\Resources\Api\V1\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->getRoleNames()->first(),
            'is_agent' => $this->hasRole('AGENT'),
            'is_jamaah' => $this->hasRole('JAMAAH'),
            'email_verified' => !!$this->email_verified_at,
        ];
    }
}


// class UserResource extends JsonResource
// {
//     public function toArray($request)
//     {
//         return [
//             // ===============================
//             // BASIC
//             // ===============================
//             'id' => $this->id,
//             'name' => $this->name,
//             'email' => $this->email,

//             // 🔥 ROLE (WAJIB ADA)
//             'role' => $this->getRoleNames()->first(),

//             // ===============================
//             // BRANCH
//             // ===============================
//             'branch' => $this->branch ? [
//                 'id' => $this->branch->id,
//                 'name' => $this->branch->name,
//             ] : null,

//             // ===============================
//             // AGENT PROFILE
//             // ===============================
//             'agent' => $this->agentProfile ? [
//                 'id' => $this->agentProfile->id,
//                 'nama' => $this->agentProfile->nama,
//                 'kode_agent' => $this->agentProfile->kode_agent,
//                 'phone' => $this->agentProfile->phone,
//                 'is_active' => (bool) $this->agentProfile->is_active,
//             ] : null,

//             // ===============================
//             // JAMAAH PROFILE
//             // ===============================
//             'jamaah' => $this->jamaahProfile ? [
//                 'id' => $this->jamaahProfile->id,
//                 'jamaah_code' => $this->jamaahProfile->jamaah_code,
//                 'nama_lengkap' => $this->jamaahProfile->nama_lengkap,
//                 'phone' => $this->jamaahProfile->phone,
//                 'city' => $this->jamaahProfile->city,
//                 'province' => $this->jamaahProfile->province,
//                 'is_active' => (bool) $this->jamaahProfile->is_active,
//             ] : null,

//             // ===============================
//             // FLAGS (SUPER IMPORTANT FRONTEND)
//             // ===============================
//             'is_agent' => $this->agentProfile !== null,
//             'is_jamaah' => $this->jamaahProfile !== null,

//             // ===============================
//             // META
//             // ===============================
//             'created_at' => $this->created_at,
//         ];
//     }
// }