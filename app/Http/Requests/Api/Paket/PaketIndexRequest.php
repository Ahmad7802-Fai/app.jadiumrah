<?php

namespace App\Http\Requests\Api\Paket;

use Illuminate\Foundation\Http\FormRequest;

class PaketIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | RULES
    |--------------------------------------------------------------------------
    */

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'departure_city' => ['nullable', 'string', 'max:100'],
            'departure_date' => ['nullable', 'date'],

            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'gte:min_price'],

            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],

            // 🔥 NEW
            'sort' => ['nullable', 'in:latest,price_low,price_high,popular'],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PREPARE
    |--------------------------------------------------------------------------
    */

    protected function prepareForValidation(): void
    {
        $this->merge([
            'search' => $this->normalizeString($this->query('search')),
            'departure_city' => $this->normalizeString($this->query('departure_city')),
            'departure_date' => $this->normalizeString($this->query('departure_date')),

            'min_price' => $this->normalizeNumeric($this->query('min_price')),
            'max_price' => $this->normalizeNumeric($this->query('max_price')),

            'per_page' => $this->normalizePerPage($this->query('per_page')),

            'sort' => $this->normalizeSort($this->query('sort')),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTERS (🔥 CLEAN OUTPUT)
    |--------------------------------------------------------------------------
    */

    public function filters(): array
    {
        return array_filter([
            'search' => $this->validated('search'),
            'departure_city' => $this->validated('departure_city'),
            'departure_date' => $this->validated('departure_date'),

            'min_price' => $this->validated('min_price'),
            'max_price' => $this->validated('max_price'),

            'per_page' => $this->validated('per_page', 12),

            'sort' => $this->validated('sort', 'latest'),
        ], fn ($v) => $v !== null);
    }

    /*
    |--------------------------------------------------------------------------
    | NORMALIZER
    |--------------------------------------------------------------------------
    */

    protected function normalizeString(mixed $value): ?string
    {
        if ($value === null) return null;

        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }

    protected function normalizeNumeric(mixed $value): int|float|null
    {
        if ($value === null || $value === '') return null;

        if (!is_numeric($value)) return null; // 🔥 FIX

        return str_contains((string) $value, '.')
            ? (float) $value
            : (int) $value;
    }

    protected function normalizePerPage(mixed $value): int
    {
        if (!is_numeric($value)) return 12;

        $value = (int) $value;

        return max(1, min($value, 100));
    }

    protected function normalizeSort(mixed $value): string
    {
        $allowed = ['latest', 'price_low', 'price_high', 'popular'];

        return in_array($value, $allowed) ? $value : 'latest';
    }

    /*
    |--------------------------------------------------------------------------
    | MESSAGES
    |--------------------------------------------------------------------------
    */

    public function messages(): array
    {
        return [
            'departure_date.date' => 'Tanggal keberangkatan tidak valid.',
            'min_price.numeric' => 'Harga minimum harus angka.',
            'max_price.numeric' => 'Harga maksimum harus angka.',
            'max_price.gte' => 'Harga maksimum harus lebih besar dari minimum.',
            'per_page.integer' => 'Per page harus angka.',
            'sort.in' => 'Sort tidak valid.',
        ];
    }
}