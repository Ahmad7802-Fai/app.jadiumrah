<?php

namespace App\Services\Commission;

use App\Models\CommissionScheme;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CommissionSchemeService
{
    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create(array $data): CommissionScheme
    {
        return DB::transaction(function () use ($data) {

            if (!empty($data['is_active'])) {
                CommissionScheme::query()->update([
                    'is_active' => false
                ]);
            }

            return CommissionScheme::create([
                'name'      => $data['name'],
                'year'      => $data['year'],
                'is_active' => $data['is_active'] ?? false,
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        CommissionScheme $scheme,
        array $data
    ): CommissionScheme {

        return DB::transaction(function () use ($scheme, $data) {

            if (!empty($data['is_active'])) {
                CommissionScheme::query()->update([
                    'is_active' => false
                ]);
            }

            $scheme->update([
                'name'      => $data['name'],
                'year'      => $data['year'],
                'is_active' => $data['is_active'] ?? false,
            ]);

            return $scheme;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function delete(CommissionScheme $scheme): void
    {
        if ($scheme->is_active) {
            throw ValidationException::withMessages([
                'scheme' => 'Active scheme cannot be deleted.'
            ]);
        }

        $scheme->delete();
    }
}