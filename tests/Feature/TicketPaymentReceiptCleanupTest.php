<?php

namespace Tests\Feature;

use App\Http\Controllers\Ticketing\TicketPaymentController;
use App\Models\TicketInvoice;
use App\Models\User;
use App\Services\Ticketing\TicketPaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class TicketPaymentReceiptCleanupTest extends TestCase
{
    private function actingAsKeuangan(): User
    {
        $user = new User;

        $user->forceFill([
            'id' => 1,
            'role' => 'KEUANGAN',
            'is_active' => true,
        ]);

        auth()->setUser($user);

        return $user;
    }

    private function payableInvoice(): TicketInvoice
    {
        $invoice = new TicketInvoice;

        $invoice->forceFill([
            'id' => 1,
            'status' => 'UNPAID',
            'total_amount' => 1000000,
            'paid_amount' => 0,
            'refunded_amount' => 0,
        ]);

        return $invoice;
    }

    public function test_failed_payment_does_not_leave_uploaded_receipt(): void
    {
        Storage::fake('public');

        $this->actingAsKeuangan();

        $request = Request::create(
            '/ticketing/invoice/1/pay',
            'POST',
            [
                'amount' => 250000,
                'method' => 'TRANSFER',
                'bank' => 'BCA',
            ],
            [],
            [
                'receipt' => UploadedFile::fake()
                    ->image('receipt.jpg'),
            ]
        );

        $service = Mockery::mock(
            TicketPaymentService::class
        );

        $service
            ->shouldReceive('pay')
            ->once()
            ->andThrow(
                new Exception('Invoice sudah lunas.')
            );

        $exception = null;

        try {
            (new TicketPaymentController)->store(
                $request,
                $this->payableInvoice(),
                $service
            );
        } catch (Exception $caught) {
            $exception = $caught;
        }

        $this->assertNotNull($exception);

        $this->assertSame(
            'Invoice sudah lunas.',
            $exception->getMessage()
        );

        $this->assertSame(
            [],
            Storage::disk('public')
                ->allFiles('payments'),
            'Receipt harus dibersihkan ketika payment gagal.'
        );
    }

    public function test_successful_payment_keeps_uploaded_receipt(): void
    {
        Storage::fake('public');

        $this->actingAsKeuangan();

        $request = Request::create(
            '/ticketing/invoice/1/pay',
            'POST',
            [
                'amount' => 250000,
                'method' => 'TRANSFER',
                'bank' => 'BCA',
            ],
            [],
            [
                'receipt' => UploadedFile::fake()
                    ->image('receipt.jpg'),
            ]
        );

        $capturedReceiptPath = null;

        $service = Mockery::mock(
            TicketPaymentService::class
        );

        $service
            ->shouldReceive('pay')
            ->once()
            ->withArgs(function (
                TicketInvoice $receivedInvoice,
                int $amount,
                int $userId,
                string $method,
                ?string $bank,
                ?string $receiptPath
            ) use (&$capturedReceiptPath): bool {
                $capturedReceiptPath = $receiptPath;

                return $receivedInvoice->id === 1
                    && $amount === 250000
                    && $userId === 1
                    && $method === 'TRANSFER'
                    && $bank === 'BCA'
                    && $receiptPath !== null;
            });

        (new TicketPaymentController)->store(
            $request,
            $this->payableInvoice(),
            $service
        );

        $this->assertNotNull(
            $capturedReceiptPath
        );

        Storage::disk('public')->assertExists(
            $capturedReceiptPath
        );

        $this->assertSame(
            [$capturedReceiptPath],
            Storage::disk('public')
                ->allFiles('payments')
        );
    }
}
