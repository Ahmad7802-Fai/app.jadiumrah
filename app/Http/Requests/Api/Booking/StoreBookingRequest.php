<?php

namespace App\Http\Requests\Api\Booking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\PaketDeparture;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /*
    |--------------------------------------------------------------------------
    | RULES
    |--------------------------------------------------------------------------
    */
    public function rules(): array
    {
        $user = $this->user();

        $rules = [

            /*
            |--------------------------------------------------------------------------
            | CORE
            |--------------------------------------------------------------------------
            */
            'paket_id' => ['nullable', 'integer', 'exists:pakets,id'],

            'paket_departure_id' => [
                'required',
                'integer',
                'exists:paket_departures,id'
            ],

            'room_type' => [
                'required',
                Rule::in(['double', 'triple', 'quad'])
            ],

            /*
            |--------------------------------------------------------------------------
            | JAMAAH
            |--------------------------------------------------------------------------
            */
            'jamaah_ids'   => ['nullable', 'array', 'min:1'],
            'jamaah_ids.*' => ['integer', 'exists:jamaahs,id'],

            'jumlah_jamaah' => ['nullable', 'integer', 'min:1'],

            /*
            |--------------------------------------------------------------------------
            | META
            |--------------------------------------------------------------------------
            */
            'channel' => ['nullable', 'string', 'max:50'],

            /*
            |--------------------------------------------------------------------------
            | BOOKER (OPSIONAL)
            |--------------------------------------------------------------------------
            */
            'booker' => ['nullable', 'array'],
            'booker.name'  => ['nullable', 'string', 'max:255'],
            'booker.phone' => ['nullable', 'string', 'max:30'],
            'booker.email' => ['nullable', 'email', 'max:255'],
        ];

        /*
        |--------------------------------------------------------------------------
        | ROLE BASED RULE
        |--------------------------------------------------------------------------
        */
        if ($user && $user->hasRole('AGENT')) {
            $rules['jamaah_ids'] = ['required', 'array', 'min:1'];
        }

        return $rules;
    }

    /*
    |--------------------------------------------------------------------------
    | AFTER VALIDATION
    |--------------------------------------------------------------------------
    */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            $departureId = $this->input('paket_departure_id');
            $paketId     = $this->input('paket_id');
            $jamaahIds   = $this->input('jamaah_ids', []);
            $jumlah      = $this->input('jumlah_jamaah');

            /*
            |--------------------------------------------------------------------------
            | VALIDATE DEPARTURE
            |--------------------------------------------------------------------------
            */
            if (!$departureId) {
                $validator->errors()->add(
                    'paket_departure_id',
                    'Departure wajib dipilih.'
                );
                return;
            }

            $departure = PaketDeparture::find($departureId);

            if (!$departure) {
                $validator->errors()->add(
                    'paket_departure_id',
                    'Departure tidak ditemukan.'
                );
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDATE PAKET MATCH
            |--------------------------------------------------------------------------
            */
            if ($paketId && $departure->paket_id !== (int) $paketId) {
                $validator->errors()->add(
                    'paket_id',
                    'Paket tidak sesuai dengan departure.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDATE JAMAAH COUNT
            |--------------------------------------------------------------------------
            */
            if (!empty($jamaahIds) && $jumlah) {
                if ((int) $jumlah !== count($jamaahIds)) {
                    $validator->errors()->add(
                        'jumlah_jamaah',
                        'Jumlah jamaah harus sesuai dengan jamaah_ids.'
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDATE ROOM EXIST
            |--------------------------------------------------------------------------
            */
            if ($departure) {
                $hasRoom = $departure->prices()
                    ->where('room_type', $this->input('room_type'))
                    ->exists();

                if (!$hasRoom) {
                    $validator->errors()->add(
                        'room_type',
                        'Tipe kamar tidak tersedia pada departure ini.'
                    );
                }
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | NORMALIZE INPUT
    |--------------------------------------------------------------------------
    */
    protected function prepareForValidation(): void
    {
        $jamaahIds = $this->input('jamaah_ids');

        $this->merge([
            'jumlah_jamaah' => $this->input('jumlah_jamaah')
                ?? (is_array($jamaahIds) ? count($jamaahIds) : null),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FINAL DATA
    |--------------------------------------------------------------------------
    */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        return [
            ...$data,

            // 🔥 ensure integer
            'paket_departure_id' => (int) $data['paket_departure_id'],

            // 🔥 fallback
            'jamaah_ids' => $data['jamaah_ids'] ?? [],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MESSAGES
    |--------------------------------------------------------------------------
    */
    public function messages(): array
    {
        return [
            'paket_departure_id.required' => 'Departure wajib dipilih.',
            'paket_departure_id.exists'   => 'Departure tidak ditemukan.',

            'room_type.required' => 'Tipe kamar wajib dipilih.',
            'room_type.in'       => 'Tipe kamar tidak valid.',

            'jamaah_ids.required' => 'Minimal 1 jamaah wajib dipilih.',
            'jamaah_ids.array'    => 'Format jamaah tidak valid.',
            'jamaah_ids.min'      => 'Minimal 1 jamaah.',
            'jamaah_ids.*.exists' => 'Data jamaah tidak valid.',

            'jumlah_jamaah.integer' => 'Jumlah jamaah harus angka.',
            'jumlah_jamaah.min'     => 'Minimal 1 jamaah.',

            'booker.email.email' => 'Format email tidak valid.',
        ];
    }
}