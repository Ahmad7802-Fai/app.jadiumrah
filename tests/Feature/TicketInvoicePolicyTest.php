<?php

namespace Tests\Feature;

use App\Models\TicketInvoice;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class TicketInvoicePolicyTest extends TestCase
{
    private function userWithRole(string $role): User
    {
        $user = new User();

        $user->forceFill([
            'role' => $role,
            'is_active' => true,
        ]);

        return $user;
    }

    private function invoice(
        string $status = 'UNPAID',
        int $paidAmount = 0
    ): TicketInvoice {
        $invoice = new TicketInvoice();

        $invoice->forceFill([
            'status' => $status,
            'paid_amount' => $paidAmount,
            'total_amount' => 1000000,
            'refunded_amount' => 0,
        ]);

        return $invoice;
    }

    public function test_keuangan_can_view_any_invoice(): void
    {
        $this->assertTrue(
            Gate::forUser(
                $this->userWithRole('KEUANGAN')
            )->allows(
                'viewAny',
                TicketInvoice::class
            )
        );
    }

    public function test_admin_cannot_view_any_invoice(): void
    {
        $this->assertFalse(
            Gate::forUser(
                $this->userWithRole('ADMIN')
            )->allows(
                'viewAny',
                TicketInvoice::class
            )
        );
    }

    public function test_keuangan_can_view_invoice(): void
    {
        $this->assertTrue(
            Gate::forUser(
                $this->userWithRole('KEUANGAN')
            )->allows(
                'view',
                $this->invoice()
            )
        );
    }

    public function test_admin_cannot_view_invoice(): void
    {
        $this->assertFalse(
            Gate::forUser(
                $this->userWithRole('ADMIN')
            )->allows(
                'view',
                $this->invoice()
            )
        );
    }

    public function test_keuangan_can_create_invoice(): void
    {
        $this->assertTrue(
            Gate::forUser(
                $this->userWithRole('KEUANGAN')
            )->allows(
                'create',
                TicketInvoice::class
            )
        );
    }

    public function test_admin_cannot_create_invoice(): void
    {
        $this->assertFalse(
            Gate::forUser(
                $this->userWithRole('ADMIN')
            )->allows(
                'create',
                TicketInvoice::class
            )
        );
    }

    public function test_keuangan_can_pay_unpaid_and_partial_invoice(): void
    {
        $user = $this->userWithRole('KEUANGAN');

        foreach (['UNPAID', 'PARTIAL'] as $status) {
            $this->assertTrue(
                Gate::forUser($user)->allows(
                    'pay',
                    $this->invoice($status)
                ),
                "KEUANGAN harus boleh membayar invoice {$status}."
            );
        }
    }

    public function test_keuangan_cannot_pay_closed_invoice_statuses(): void
    {
        $user = $this->userWithRole('KEUANGAN');

        foreach (['PAID', 'REFUNDED'] as $status) {
            $this->assertFalse(
                Gate::forUser($user)->allows(
                    'pay',
                    $this->invoice($status)
                ),
                "KEUANGAN tidak boleh membayar invoice {$status}."
            );
        }
    }

    public function test_admin_cannot_pay_invoice(): void
    {
        $this->assertFalse(
            Gate::forUser(
                $this->userWithRole('ADMIN')
            )->allows(
                'pay',
                $this->invoice('UNPAID')
            )
        );
    }

    public function test_keuangan_can_refund_paid_and_partial_invoice(): void
    {
        $user = $this->userWithRole('KEUANGAN');

        foreach (['PAID', 'PARTIAL'] as $status) {
            $this->assertTrue(
                Gate::forUser($user)->allows(
                    'refund',
                    $this->invoice($status)
                ),
                "KEUANGAN harus boleh refund invoice {$status}."
            );
        }
    }

    public function test_keuangan_cannot_refund_non_refundable_statuses(): void
    {
        $user = $this->userWithRole('KEUANGAN');

        foreach (['UNPAID', 'REFUNDED'] as $status) {
            $this->assertFalse(
                Gate::forUser($user)->allows(
                    'refund',
                    $this->invoice($status)
                ),
                "KEUANGAN tidak boleh refund invoice {$status}."
            );
        }
    }

    public function test_admin_cannot_refund_paid_invoice(): void
    {
        $this->assertFalse(
            Gate::forUser(
                $this->userWithRole('ADMIN')
            )->allows(
                'refund',
                $this->invoice('PAID')
            )
        );
    }
}
