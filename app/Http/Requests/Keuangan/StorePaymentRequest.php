<?php

namespace App\Http\Requests\Keuangan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Invoices;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // auth dijaga middleware
    }

    public function rules(): array
    {
        return [
            'jamaah_id'      => 'required|exists:jamaah,id',
            'jumlah'         => 'required|numeric|min:1000',
            'tanggal_bayar'  => 'required|date',
            'metode'         => 'required|in:transfer,cash,kantor,gateway',
            'bukti_transfer' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'keterangan'     => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'jamaah_id.required' => 'Jamaah harus dipilih.',
            'jamaah_id.exists'   => 'Jamaah tidak ditemukan.',

            'jumlah.required' => 'Jumlah pembayaran harus diisi.',
            'jumlah.min'      => 'Jumlah pembayaran minimal Rp 1.000.',

            'metode.required' => 'Metode pembayaran wajib dipilih.',
            'metode.in'       => 'Metode pembayaran tidak valid.',

            'tanggal_bayar.required' => 'Tanggal bayar wajib diisi.',
            'tanggal_bayar.date'     => 'Format tanggal tidak valid.',

            'bukti_transfer.mimes' => 'Bukti transfer harus JPG, PNG, atau PDF.',
            'bukti_transfer.max'   => 'Ukuran file maksimal 4MB.',
        ];
    }

    protected function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {

            // MODE CICILAN → invoice_id dari query string
            $invoiceId = $this->query('invoice_id');

            if (! $invoiceId) {
                return; // pembayaran baru
            }

            $invoice = Invoices::find($invoiceId);

            if (! $invoice) {
                $validator->errors()->add(
                    'jumlah',
                    'Invoice tidak ditemukan.'
                );
                return;
            }

            // 🔒 Guard invoice status
            if ($invoice->status === 'lunas') {
                $validator->errors()->add(
                    'jumlah',
                    'Invoice sudah lunas, tidak bisa menambah cicilan.'
                );
                return;
            }

            // 🔒 Guard nominal
            if ($this->jumlah > $invoice->sisa_tagihan) {
                $validator->errors()->add(
                    'jumlah',
                    'Jumlah cicilan melebihi sisa tagihan.'
                );
            }
        });
    }
}
