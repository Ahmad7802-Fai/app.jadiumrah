<?php
namespace App\Services\Jamaah;

use App\Models\Jamaah;
use App\Services\Jamaah\Concerns\FiltersEditableFields;

class JamaahCoreService
{
    use FiltersEditableFields;

    public function update(Jamaah $jamaah, array $data): Jamaah
    {
        $old = $jamaah->toArray();

        $data = $this->filter($jamaah, $data);

        $jamaah->update($data);

        JamaahAuditService::log(
            $jamaah,
            'UPDATE',
            $old,
            $data
        );

        return $jamaah;
    }
}
