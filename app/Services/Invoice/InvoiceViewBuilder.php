<?php

namespace App\Services\Invoice;

use App\Models\Invoices;
use App\Models\Jamaah;
use App\Support\CompanyProfile;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InvoiceViewBuilder
{
    use CompanyProfile;

    public function build(Invoices $invoice, Jamaah $jamaah): array
    {
        return [
            'company' => $this->companyProfile(),

            'invoice' => $invoice,
            'jamaah'  => $jamaah,

            'history' => $invoice->payments()
                ->where('status', 'valid')
                ->where('is_deleted', 0)
                ->orderBy('tanggal_bayar', 'asc')
                ->get(),

            'qrCode' => $this->generateQr($invoice),
        ];
    }

    protected function generateQr(Invoices $invoice): string
    {
        return base64_encode(
            QrCode::format('svg')
                ->size(120)
                ->generate($this->invoiceHash($invoice))
        );
    }

    protected function invoiceHash(Invoices $invoice): string
    {
        return hash('sha256', implode('|', [
            $invoice->id,
            $invoice->nomor_invoice,
            $invoice->created_at?->timestamp,
            config('app.key'),
        ]));
    }

}
