<?php

namespace App\Exports;

use App\Services\Ticketing\TicketReportService;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
};

class RefundReportExport implements
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
     * Ambil data refund dari service
     */
    public function collection()
    {
        return collect(
            app(TicketReportService::class)
                ->refunds($this->from, $this->to)
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
            'Refund Date',
            'Amount',
            'Status',
            'Reason',
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
            $row->refunded_at,
            $row->amount,
            $row->status,
            $row->reason,
        ];
    }
}
