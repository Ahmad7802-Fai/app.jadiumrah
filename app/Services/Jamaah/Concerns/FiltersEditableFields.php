<?php

namespace App\Services\Jamaah\Concerns;

use App\Models\Jamaah;

trait FiltersEditableFields
{
    protected function filter(Jamaah $jamaah, array $data): array
    {
        $user = auth()->user();

        if ($user->hasRole('agent')) {
            unset(
                $data['nik'],
                $data['tanggal_lahir'],
                $data['jenis_kelamin'],
                $data['status_pernikahan']
            );
        }

        if ($user->hasRole('cabang')) {
            unset($data['nik']);
        }

        return $data;
    }
}
