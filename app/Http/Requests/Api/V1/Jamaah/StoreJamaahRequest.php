<?php

namespace App\Http\Requests\Api\V1\Jamaah;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJamaahRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $gender = $this->input('gender');
        $passport = $this->input('passport_number');
        $email = $this->input('email');
        $nik = $this->input('nik');
        $phone = $this->input('phone');

        $normalizedGender = null;

        if ($gender !== null && $gender !== '') {
            $g = mb_strtolower(trim((string) $gender));

            $normalizedGender = match ($g) {
                'l', 'lk', 'laki', 'laki laki', 'laki-laki', 'male', 'pria' => 'laki-laki',
                'p', 'pr', 'perempuan', 'wanita', 'female' => 'perempuan',
                default => $g,
            };
        }

        $this->merge([
            'nama_lengkap' => $this->filled('nama_lengkap')
                ? trim((string) $this->input('nama_lengkap'))
                : null,

            'tempat_lahir' => $this->filled('tempat_lahir')
                ? trim((string) $this->input('tempat_lahir'))
                : null,

            'gender' => $normalizedGender,

            'nik' => $this->filled('nik')
                ? preg_replace('/\D+/', '', (string) $nik)
                : null,

            'passport_number' => $this->filled('passport_number')
                ? strtoupper(trim((string) $passport))
                : null,

            'phone' => $this->filled('phone')
                ? preg_replace('/\s+/', '', trim((string) $phone))
                : null,

            'email' => $this->filled('email')
                ? mb_strtolower(trim((string) $email))
                : null,

            'address' => $this->filled('address')
                ? trim((string) $this->input('address'))
                : null,

            'city' => $this->filled('city')
                ? trim((string) $this->input('city'))
                : null,

            'province' => $this->filled('province')
                ? trim((string) $this->input('province'))
                : null,

            'family_id' => $this->filled('family_id')
                ? trim((string) $this->input('family_id'))
                : null,
        ]);
    }

    public function rules(): array
    {
        $jamaahId = $this->route('jamaah')?->id ?? $this->route('jamaah');

        return [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'agent_id' => ['nullable', 'integer', 'exists:users,id'],
            'family_id' => ['nullable', 'string', 'max:50'],

            'source' => ['nullable', Rule::in(['offline', 'branch', 'agent', 'website'])],

            'nama_lengkap' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', Rule::in(['laki-laki', 'perempuan'])],
            'tanggal_lahir' => ['nullable', 'date'],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],

            'nik' => [
                'nullable',
                'digits_between:8,25',
                Rule::unique('jamaahs', 'nik')->ignore($jamaahId),
            ],

            'passport_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('jamaahs', 'passport_number')->ignore($jamaahId),
            ],

            'seat_number' => ['nullable', 'string', 'max:10'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],

            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],

            'is_active' => ['nullable', 'boolean'],
            'approval_status' => ['nullable', Rule::in(['pending', 'approved', 'rejected'])],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'gender.in' => 'Gender harus laki-laki atau perempuan.',
            'nik.digits_between' => 'NIK harus berupa angka dengan panjang 8 sampai 25 digit.',
            'nik.unique' => 'NIK sudah digunakan oleh jamaah lain.',
            'passport_number.unique' => 'Nomor paspor sudah digunakan oleh jamaah lain.',
            'passport_number.max' => 'Nomor paspor terlalu panjang.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email terlalu panjang.',
            'source.in' => 'Source jamaah tidak valid.',
            'approval_status.in' => 'Status approval tidak valid.',
        ];
    }
}