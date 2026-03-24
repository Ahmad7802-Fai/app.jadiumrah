<?php

namespace App\Http\Resources\Api\V1\Jamaah;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JamaahResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $approvalStatus = (string) $this->approval_status;
        $source = (string) $this->source;
        $isActive = (bool) $this->is_active;

        return [
            'id' => $this->id,
            'jamaah_code' => $this->jamaah_code,

            'user_id' => $this->user_id,
            'branch_id' => $this->branch_id,
            'agent_id' => $this->agent_id,

            'family_id' => $this->family_id,

            'source' => $source,
            'source_label' => $this->sourceLabel($source),

            'nama_lengkap' => $this->nama_lengkap,
            'gender' => $this->gender,
            'tanggal_lahir' => $this->tanggal_lahir,
            'tanggal_lahir_label' => $this->formatDate($this->tanggal_lahir),
            'tempat_lahir' => $this->tempat_lahir,

            'nik' => $this->nik,
            'passport_number' => $this->passport_number,
            'seat_number' => $this->seat_number,

            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'city' => $this->city,
            'province' => $this->province,

            'is_active' => $isActive,
            'is_active_label' => $isActive ? 'Aktif' : 'Nonaktif',

            'approval_status' => $approvalStatus,
            'approval_status_label' => $this->approvalStatusLabel($approvalStatus),

            'created_at' => $this->created_at,
            'created_at_label' => $this->formatDateTime($this->created_at),

            'updated_at' => $this->updated_at,
            'updated_at_label' => $this->formatDateTime($this->updated_at),

            'actions' => [
                'can_edit' => true,
                'can_delete' => true,
                'can_approve' => $approvalStatus === 'pending',
                'can_reject' => $approvalStatus === 'pending',
            ],

            'links' => [
                'self' => route('api.jamaahs.show', $this->id),
                'approve' => $approvalStatus === 'pending'
                    ? route('api.jamaahs.approve', $this->id)
                    : null,
                'reject' => $approvalStatus === 'pending'
                    ? route('api.jamaahs.reject', $this->id)
                    : null,
            ],
        ];
    }

    protected function approvalStatusLabel(?string $status): ?string
    {
        return match ($status) {
            'pending' => 'Menunggu Approval',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => $status,
        };
    }

    protected function sourceLabel(?string $source): ?string
    {
        return match ($source) {
            'offline' => 'Offline',
            'branch' => 'Cabang',
            'agent' => 'Agent',
            'website' => 'Website',
            default => $source,
        };
    }

    protected function formatDateTime($date): ?string
    {
        if (!$date) {
            return null;
        }

        return Carbon::parse($date)->translatedFormat('d M Y H:i');
    }

    protected function formatDate($date): ?string
    {
        if (!$date) {
            return null;
        }

        return Carbon::parse($date)->translatedFormat('d M Y');
    }

    protected function actions(): array
    {
        $status = (string) $this->approval_status;

        return [
            'can_edit' => in_array($status, ['pending', 'rejected', 'approved'], true),
            'can_delete' => in_array($status, ['pending', 'rejected'], true),
            'can_approve' => $status === 'pending',
            'can_reject' => $status === 'pending',
        ];
    }

}