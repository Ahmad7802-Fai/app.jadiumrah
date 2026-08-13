<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketPnr extends Model
{
    protected $table = 'ticket_pnrs';

    /* =========================
     | MASS ASSIGNMENT
     ========================= */
    protected $fillable = [
        'pnr_code',
        'client_id',
        'lobc_code',
        'agent_id',
        'branch_id',
        'category',

        // AIRLINE
        'airline_code',
        'airline_name',
        'airline_class',

        // CORE
        'pax',
        'fare_per_pax',
        'total_fare',

        'seat',
        'status',
        'po_status',
        'created_by',
    ];

    /* =========================
     | CASTING
     ========================= */
    protected $casts = [
        'pax'          => 'integer',
        'fare_per_pax' => 'integer',
        'total_fare'   => 'integer',
    ];

    /* =========================
     | RELATIONSHIPS
     ========================= */

    public function routes()
    {
        return $this->hasMany(TicketRoute::class, 'pnr_id')
            ->orderBy('sector');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function invoices()
    {
        return $this->hasMany(TicketInvoice::class, 'pnr_id');
    }

    /* =========================
     | DERIVED FINANCIAL (READ ONLY)
     ========================= */

    public function getInvoiceTotalAttribute(): int
    {
        return $this->invoices()->sum('total_amount');
    }

    public function getInvoicePaidAttribute(): int
    {
        return $this->invoices()->sum('paid_amount');
    }

    public function getInvoiceOutstandingAttribute(): int
    {
        return max(
            0,
            $this->invoice_total - $this->invoice_paid
        );
    }

    /* =========================
     | UI HELPERS
     ========================= */

    public function getFareLabelAttribute(): string
    {
        return number_format($this->fare_per_pax, 0, ',', '.');
    }

    public function getTotalFareLabelAttribute(): string
    {
        return number_format($this->total_fare, 0, ',', '.');
    }

    public function getAirlineLabelAttribute(): string
    {
        if ($this->airline_code && $this->airline_name) {
            return "{$this->airline_name} ({$this->airline_code})";
        }

        return $this->airline_name ?? '-';
    }

    public function getFinancialStatusAttribute(): string
    {
        if ($this->invoice_paid <= 0) {
            return 'UNPAID';
        }

        if ($this->invoice_outstanding <= 0) {
            return 'PAID';
        }

        return 'PARTIAL';
    }

public function allocations()
{
    return $this->hasMany(
        \App\Models\TicketAllocation::class,
        'pnr_id'
    )->orderBy('allocation_date');
}


}
