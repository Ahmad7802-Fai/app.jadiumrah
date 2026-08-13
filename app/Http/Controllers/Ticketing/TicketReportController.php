<?php

namespace App\Http\Controllers\Ticketing;

use App\Http\Controllers\Controller;
use App\Services\Ticketing\TicketReportService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PaymentReportExport;
use App\Exports\RefundReportExport;

class TicketReportController extends Controller
{
    public function __construct(
        protected TicketReportService $service
    ) {}

    /* =================================================
     | FORM FILTER
     ================================================= */
    public function index()
    {
        $this->authorize('viewReport');

        return view('ticketing.report.index');
    }

    /* =================================================
     | PAYMENT PDF
     ================================================= */
    public function paymentPdf(Request $request)
    {
        [$from, $to] = $this->validatedRange($request);

        $payments = $this->service->payments($from, $to);

        return Pdf::loadView(
            'pdf.report_payment',
            compact('payments', 'from', 'to')
        )
            ->setPaper('A4', 'portrait')
            ->download($this->filename('Payment', $from, $to, 'pdf'));
    }

    /* =================================================
     | PAYMENT EXCEL
     ================================================= */
    public function paymentExcel(Request $request)
    {
        [$from, $to] = $this->validatedRange($request);

        return Excel::download(
            new PaymentReportExport($from, $to),
            $this->filename('Payment', $from, $to, 'xlsx')
        );
    }

    /* =================================================
     | REFUND PDF
     ================================================= */
    public function refundPdf(Request $request)
    {
        [$from, $to] = $this->validatedRange($request);

        $refunds = $this->service->refunds($from, $to);

        return Pdf::loadView(
            'pdf.report_refund',
            compact('refunds', 'from', 'to')
        )
            ->setPaper('A4', 'portrait')
            ->download($this->filename('Refund', $from, $to, 'pdf'));
    }

    /* =================================================
     | REFUND EXCEL
     ================================================= */
    public function refundExcel(Request $request)
    {
        [$from, $to] = $this->validatedRange($request);

        return Excel::download(
            new RefundReportExport($from, $to),
            $this->filename('Refund', $from, $to, 'xlsx')
        );
    }

    /* =================================================
     | SHARED METHODS
     ================================================= */

    private function validatedRange(Request $request): array
    {
        $this->authorize('viewReport');

        $data = $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date|after_or_equal:from',
        ]);

        return [$data['from'], $data['to']];
    }

    private function filename(
        string $type,
        string $from,
        string $to,
        string $ext
    ): string {
        return sprintf(
            'Laporan-%s-%s-sampai-%s.%s',
            $type,
            $from,
            $to,
            $ext
        );
    }
}
