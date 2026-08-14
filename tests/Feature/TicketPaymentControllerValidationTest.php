<?php

namespace Tests\Feature;

use App\Http\Controllers\Ticketing\TicketPaymentController;
use App\Models\TicketInvoice;
use App\Models\User;
use App\Services\Ticketing\TicketPaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class TicketPaymentControllerValidationTest extends TestCase
{
    private function actingAsFinance(): User
    {
        $user = new User();

        $user->forceFill([
            'id' => 1,
            'role' => 'KEUANGAN',
            'is_active' => true,
        ]);

        auth()->setUser($user);

        return $user;
    }

    private function unpaidInvoice(): TicketInvoice
    {
        $invoice = new TicketInvoice();

        $invoice->forceFill([
            'id' => 1,
            'status' => 'UNPAID',
            'total_amount' => 1000000,
            'paid_amount' => 0,
            'refunded_amount' => 0,
        ]);

        return $invoice;
    }

    public function test_store_rejects_unknown_payment_method(): void
    {
        $this->actingAsFinance();

        $service = Mockery::mock(
            TicketPaymentService::class
        );

        $service->shouldNotReceive('pay');

        $request = Request::create(
            '/ticketing/invoice/1/pay',
            'POST',
            [
                'amount' => 100000,
                'method' => 'CRYPTO',
            ]
        );

        $this->expectException(
            ValidationException::class
        );

        (new TicketPaymentController())->store(
            $request,
            $this->unpaidInvoice(),
            $service
        );
    }

    public function test_store_rejects_bank_longer_than_schema_limit(): void
    {
        $this->actingAsFinance();

        $service = Mockery::mock(
            TicketPaymentService::class
        );

        $service->shouldNotReceive('pay');

        $request = Request::create(
            '/ticketing/invoice/1/pay',
            'POST',
            [
                'amount' => 100000,
                'method' => 'TRANSFER',
                'bank' => str_repeat('B', 51),
            ]
        );

        $this->expectException(
            ValidationException::class
        );

        (new TicketPaymentController())->store(
            $request,
            $this->unpaidInvoice(),
            $service
        );
    }

    public function test_store_rejects_unsupported_receipt_type(): void
    {
        $this->actingAsFinance();

        $service = Mockery::mock(
            TicketPaymentService::class
        );

        $service->shouldNotReceive('pay');

        $request = Request::create(
            '/ticketing/invoice/1/pay',
            'POST',
            [
                'amount' => 100000,
                'method' => 'TRANSFER',
            ],
            [],
            [
                'receipt' => UploadedFile::fake()->create(
                    'receipt.exe',
                    10,
                    'application/octet-stream'
                ),
            ]
        );

        $this->expectException(
            ValidationException::class
        );

        (new TicketPaymentController())->store(
            $request,
            $this->unpaidInvoice(),
            $service
        );
    }

    public function test_store_rejects_receipt_larger_than_two_megabytes(): void
    {
        $this->actingAsFinance();

        $service = Mockery::mock(
            TicketPaymentService::class
        );

        $service->shouldNotReceive('pay');

        $request = Request::create(
            '/ticketing/invoice/1/pay',
            'POST',
            [
                'amount' => 100000,
                'method' => 'TRANSFER',
            ],
            [],
            [
                'receipt' => UploadedFile::fake()->create(
                    'receipt.pdf',
                    2049,
                    'application/pdf'
                ),
            ]
        );

        $this->expectException(
            ValidationException::class
        );

        (new TicketPaymentController())->store(
            $request,
            $this->unpaidInvoice(),
            $service
        );
    }
}
