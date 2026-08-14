<?php

namespace Tests\Feature;

use App\Http\Controllers\Ticketing\TicketInvoiceController;
use App\Http\Controllers\Ticketing\TicketInvoicePdfController;
use App\Models\TicketInvoice;
use App\Models\TicketPnr;
use App\Models\User;
use App\Services\Ticketing\TicketInvoiceService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class TicketInvoiceControllerAuthorizationTest extends TestCase
{
    private function actingAsOperator(): User
    {
        $user = new User();

        $user->forceFill([
            'role' => 'OPERATOR',
            'is_active' => true,
        ]);

        auth()->setUser($user);

        return $user;
    }

    public function test_index_requires_view_any_policy(): void
    {
        $this->actingAsOperator();

        $controller = new TicketInvoiceController();

        $this->expectException(
            AuthorizationException::class
        );

        $controller->index(
            Request::create('/ticketing/invoice', 'GET')
        );
    }

    public function test_show_requires_view_policy(): void
    {
        $this->actingAsOperator();

        $controller = new TicketInvoiceController();
        $invoice = new TicketInvoice();

        $invoice->forceFill([
            'id' => 1,
            'status' => 'UNPAID',
            'total_amount' => 1000000,
            'paid_amount' => 0,
            'refunded_amount' => 0,
        ]);

        $this->expectException(
            AuthorizationException::class
        );

        $controller->show($invoice);
    }

    public function test_store_from_pnr_requires_create_policy(): void
    {
        $this->actingAsOperator();

        $controller = new TicketInvoiceController();

        $pnr = new TicketPnr();

        $pnr->forceFill([
            'id' => 1,
            'status' => 'CONFIRMED',
        ]);

        $service = Mockery::mock(
            TicketInvoiceService::class
        );

        $service
            ->shouldNotReceive('createFromPnr');

        $this->expectException(
            AuthorizationException::class
        );

        $controller->storeFromPnr(
            $pnr,
            $service
        );
    }

    public function test_pdf_requires_view_policy(): void
    {
        $this->actingAsOperator();

        $controller = new TicketInvoicePdfController();

        $invoice = new TicketInvoice();

        $invoice->forceFill([
            'id' => 1,
            'status' => 'UNPAID',
            'total_amount' => 1000000,
            'paid_amount' => 0,
            'refunded_amount' => 0,
        ]);

        $this->expectException(
            AuthorizationException::class
        );

        $controller->show($invoice);
    }
}
