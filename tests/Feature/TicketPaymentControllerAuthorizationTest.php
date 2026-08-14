<?php

namespace Tests\Feature;

use App\Http\Controllers\Ticketing\TicketPaymentController;
use App\Http\Controllers\Ticketing\TicketPaymentPdfController;
use App\Models\TicketInvoice;
use App\Models\TicketPayment;
use App\Models\User;
use App\Services\Ticketing\TicketPaymentService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class TicketPaymentControllerAuthorizationTest extends TestCase
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

    public function test_store_requires_pay_policy_before_service(): void
    {
        $this->actingAsOperator();

        $controller = new TicketPaymentController();

        $invoice = new TicketInvoice();

        $invoice->forceFill([
            'id' => 1,
            'status' => 'UNPAID',
            'total_amount' => 1000000,
            'paid_amount' => 0,
            'refunded_amount' => 0,
        ]);

        $request = Request::create(
            '/ticketing/invoice/1/pay',
            'POST',
            [
                'amount' => 100000,
                'method' => 'CASH',
            ]
        );

        $service = Mockery::mock(
            TicketPaymentService::class
        );

        $service->shouldNotReceive('pay');

        $this->expectException(
            AuthorizationException::class
        );

        $controller->store(
            $request,
            $invoice,
            $service
        );
    }

    public function test_receipt_requires_view_policy(): void
    {
        $this->actingAsOperator();

        $controller = new TicketPaymentPdfController();

        $payment = new TicketPayment();

        $payment->forceFill([
            'id' => 1,
            'ticket_invoice_id' => 1,
            'amount' => 100000,
            'status' => 'VALID',
        ]);

        $this->expectException(
            AuthorizationException::class
        );

        $controller->receipt($payment);
    }
}
