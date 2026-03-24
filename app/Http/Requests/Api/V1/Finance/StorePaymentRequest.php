<?php

namespace App\Http\Requests\Api\V1\Finance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Booking;

class StorePaymentRequest extends FormRequest
{
    protected ?Booking $booking = null;

    /*
    |--------------------------------------------------------------------------
    | AUTHORIZE
    |--------------------------------------------------------------------------
    */

    public function authorize(): bool
    {
        return auth()->check();
    }

    /*
    |--------------------------------------------------------------------------
    | PREPARE DATA
    |--------------------------------------------------------------------------
    */

    protected function prepareForValidation(): void
    {
        $this->booking = $this->route('booking');

        // NUMERIC
        if ($this->has('amount')) {
            $this->merge([
                'amount' => (float) $this->input('amount'),
            ]);
        }

        if ($this->has('fee_amount')) {
            $this->merge([
                'fee_amount' => (float) $this->input('fee_amount'),
            ]);
        }

        // 🔥 FIX UTAMA DI SINI
        if ($this->has('method')) {
            $this->merge([
                'method' => trim(strtolower($this->input('method'))),
            ]);
        }

        if ($this->has('type')) {
            $this->merge([
                'type' => trim(strtolower($this->input('type'))),
            ]);
        }

        if ($this->has('channel')) {
            $this->merge([
                'channel' => trim(strtolower($this->input('channel'))),
            ]);
        }

        if (!$this->has('channel')) {
            $this->merge([
                'channel' => 'website',
            ]);
        }

        if (!$this->has('type')) {
            $this->merge([
                'type' => 'dp',
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RULES (SYNC DB)
    |--------------------------------------------------------------------------
    */

    public function rules(): array
    {
        return [

            // OPTIONAL RELATION
            'jamaah_id' => [
                'nullable',
                'integer',
                Rule::exists('jamaahs', 'id'),
            ],

            // MONEY
            'amount' => [
                'required',
                'numeric',
                'min:1',
            ],

            'fee_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            // ENUM (SYNC DB)
            'type' => [
                'required',
                Rule::in(['dp','cicilan','pelunasan','add_on','upgrade','adjustment']),
            ],

            'method' => [
                'required',
                Rule::in(['transfer','cash','gateway','edc']),
            ],

            'channel' => [
                'required',
                Rule::in(['website','agent','admin','gateway']),
            ],

            // OPTIONAL DATA
            'reference_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'paid_at' => [
                'nullable',
                'date',
            ],

            'note' => [
                'nullable',
                'string',
            ],

            // 🔥 FILE (FIXED dari sebelumnya string)
            'proof_file' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | BUSINESS VALIDATION (FINTECH LEVEL)
    |--------------------------------------------------------------------------
    */

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $booking = $this->booking;

            if (!$booking) {
                $validator->errors()->add('booking', 'Booking tidak ditemukan.');
                return;
            }

            // 🔥 STATUS GUARD
            if ($booking->isExpired()) {
                $validator->errors()->add('booking', 'Booking sudah expired.');
            }

            if (in_array($booking->status, ['cancelled', 'confirmed'], true)) {
                $validator->errors()->add('booking', 'Booking tidak bisa dibayar.');
            }

            // 🔥 HITUNG REAL PAYMENT
            $paid = (float) $booking->payments()
                ->where('status', 'paid')
                ->sum('amount');

            $total = (float) $booking->total_amount;
            $remaining = max(0, $total - $paid);

            $amount = (float) $this->input('amount');

            // 🔥 OVERPAY PROTECTION
            if ($amount > $remaining) {
                $validator->errors()->add(
                    'amount',
                    'Pembayaran melebihi sisa tagihan.'
                );
            }

            // 🔥 SUDAH LUNAS
            if ($remaining <= 0) {
                $validator->errors()->add(
                    'booking',
                    'Booking sudah lunas.'
                );
            }

            // 🔥 JAMAAH VALIDATION
            if ($this->filled('jamaah_id')) {

                $exists = $booking->jamaahs()
                    ->where('jamaahs.id', $this->jamaah_id)
                    ->exists();

                if (!$exists) {
                    $validator->errors()->add(
                        'jamaah_id',
                        'Jamaah tidak termasuk dalam booking ini.'
                    );
                }
            }

            // 🔥 ANTI SPAM (RATE LIMIT MINI)
            $recent = $booking->payments()
                ->where('created_by', auth()->id())
                ->where('created_at', '>=', now()->subSeconds(5))
                ->count();

            if ($recent > 3) {
                $validator->errors()->add(
                    'amount',
                    'Terlalu banyak percobaan pembayaran. Coba lagi sebentar.'
                );
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTES
    |--------------------------------------------------------------------------
    */

    public function attributes(): array
    {
        return [
            'jamaah_id' => 'jamaah',
            'amount' => 'jumlah pembayaran',
            'fee_amount' => 'biaya admin',
            'method' => 'metode pembayaran',
            'type' => 'tipe pembayaran',
            'channel' => 'channel pembayaran',
            'reference_number' => 'nomor referensi',
            'paid_at' => 'tanggal bayar',
            'note' => 'catatan',
            'proof_file' => 'bukti pembayaran',
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
            'amount.required' => 'Jumlah pembayaran wajib diisi.',
            'amount.numeric' => 'Jumlah pembayaran harus berupa angka.',
            'amount.min' => 'Jumlah pembayaran minimal 1.',

            'fee_amount.numeric' => 'Biaya admin harus berupa angka.',
            'fee_amount.min' => 'Biaya admin tidak boleh kurang dari 0.',

            'method.required' => 'Metode pembayaran wajib dipilih.',
            'method.in' => 'Metode pembayaran tidak valid.',

            'type.required' => 'Tipe pembayaran wajib diisi.',
            'type.in' => 'Tipe pembayaran tidak valid.',

            'channel.required' => 'Channel wajib diisi.',
            'channel.in' => 'Channel pembayaran tidak valid.',

            'reference_number.max' => 'Nomor referensi maksimal 100 karakter.',

            'paid_at.date' => 'Tanggal bayar tidak valid.',

            'proof_file.file' => 'Bukti pembayaran harus berupa file.',
            'proof_file.mimes' => 'Format file harus JPG, PNG, atau PDF.',
            'proof_file.max' => 'Ukuran file maksimal 2MB.',
        ];
    }

    public function validationData()
    {
        return array_merge(
            $this->all(),
            $this->json()->all()
        );
    }

}