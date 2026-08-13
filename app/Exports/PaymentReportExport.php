<?php

namespace App\Exports;

use App\Services\Ticketing\TicketReportService;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
};

class PaymentReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
{
    protected string $from;
    protected string $to;

    public function __construct(string $from, string $to)
    {
        $this->from = $from;
        $this->to   = $to;
    }

    /**
     * Ambil data dari service (single source of truth)
     */
    public function collection()
    {
        return collect(
            app(TicketReportService::class)
                ->payments($this->from, $this->to)
        );
    }

    /**
     * Header kolom Excel
     */
    public function headings(): array
    {
        return [
            'Invoice Number',
            'PNR Code',
            'Payment Date',
            'Amount',
            'Invoice Status',
        ];
    }

    /**
     * Mapping per baris
     */
    public function map($row): array
    {
        return [
            $row->invoice_number,
            $row->pnr_code,
            $row->payment_date,
            $row->amount,
            $row->status,
        ];
    }
}
