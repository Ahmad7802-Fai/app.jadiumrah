<?php

namespace App\Http\Requests\Keuangan;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true; // akses sudah dilindungi middleware
    }

    public function rules()
    {
        return [
            'jumlah'        => 'required|numeric|min:1',
            'metode'        => 'required|in:transfer,cash,kantor,gateway',
            'tanggal_bayar' => 'required|date',
            'keterangan'    => 'nullable|string|max:500',

            // file baru opsional
            'bukti'         => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ];
    }

    public function messages()
    {
        return [
            'jumlah.required' => 'Jumlah pembayaran harus diisi.',
            'metode.required' => 'Metode pembayaran wajib dipilih.',
            'tanggal_bayar.required' => 'Tanggal bayar wajib diisi.',
        ];
    }
}
