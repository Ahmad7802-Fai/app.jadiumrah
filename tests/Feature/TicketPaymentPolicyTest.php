<?php

namespace Tests\Feature;

use App\Models\TicketPayment;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class TicketPaymentPolicyTest extends TestCase
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

    private function payment(): TicketPayment
    {
        $payment = new TicketPayment();

        $payment->forceFill([
            'id' => 1,
            'ticket_invoice_id' => 1,
            'amount' => 100000,
            'status' => 'VALID',
        ]);

        return $payment;
    }

    public function test_keuangan_can_view_ticket_payment(): void
    {
        $this->assertTrue(
            Gate::forUser(
                $this->userWithRole('KEUANGAN')
            )->allows(
                'view',
                $this->payment()
            )
        );
    }

    public function test_admin_cannot_view_ticket_payment(): void
    {
        $this->assertFalse(
            Gate::forUser(
                $this->userWithRole('ADMIN')
            )->allows(
                'view',
                $this->payment()
            )
        );
    }
}
