<?php

namespace App\Http\Controllers\Api\V1\Booking;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BookingDocumentController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | INVOICE
    |--------------------------------------------------------------------------
    */

public function invoice(Booking $booking)
{
    if (!$booking->invoice_number) {
        abort(404,'Invoice belum tersedia');
    }

    $booking->load([
        'jamaahs',
        'paket',
        'payments'
    ]);

    $qr = base64_encode(
        QrCode::format('png')
        ->size(120)
        ->generate(url('/invoice/'.$booking->invoice_number))
    );

    $pdf = Pdf::loadView(
        'documents.invoice_pdf',
        compact('booking','qr')
    )->setOptions([
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true
    ]);

    return $pdf->stream(
        $booking->invoice_number.'.pdf'
    );
}

    /*
    |--------------------------------------------------------------------------
    | RECEIPT
    |--------------------------------------------------------------------------
    */

    public function receipt(Booking $booking)
    {

        $booking->load([
            'paket',
            'departure',
            'jamaahs',
            'payments' => fn($q) => $q->where('status','paid')
        ]);

        /*
        |--------------------------------------------------------------------------
        | QR RECEIPT
        |--------------------------------------------------------------------------
        */

        $qr = base64_encode(
            QrCode::format('png')
                ->size(100)
                ->generate(
                    url('/verify/receipt/'.$booking->booking_code)
                )
        );

        $pdf = Pdf::loadView(
            'documents.receipt_pdf',
            [
                'booking' => $booking,
                'qr' => $qr
            ]
        )->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans'
        ]);

        return $pdf->stream(
            "receipt-{$booking->booking_code}.pdf"
        );

    }

}