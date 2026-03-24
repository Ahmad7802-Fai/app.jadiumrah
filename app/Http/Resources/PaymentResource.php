<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'payment_code' => $this->payment_code,

            'receipt_number' => $this->receipt_number,

            'invoice_number' => $this->invoice_number,

            'booking_id' => $this->booking_id,

            'jamaah_id' => $this->jamaah_id,

            'paket_departure_id' => $this->paket_departure_id,

            'type' => $this->type,

            'method' => $this->method,

            'amount' => (float) $this->amount,

            'status' => $this->status,

            'paid_at' => $this->paid_at,

            'note' => $this->note,

            'proof_file' => $this->proof_file
                ? asset('storage/'.$this->proof_file)
                : null,

            'created_by' => $this->created_by,

            'approved_by' => $this->approved_by,

            'approved_at' => $this->approved_at,

            'created_at' => $this->created_at,

        ];
    }
}