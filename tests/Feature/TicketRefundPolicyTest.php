<?php

namespace Tests\Feature;

use App\Models\TicketRefund;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class TicketRefundPolicyTest extends TestCase
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

    private function refund(
        string $approvalStatus = 'PENDING'
    ): TicketRefund {
        $refund = new TicketRefund();

        $refund->forceFill([
            'id' => 1,
            'ticket_invoice_id' => 1,
            'amount' => 100000,
            'status' => 'REQUESTED',
            'approval_status' => $approvalStatus,
        ]);

        return $refund;
    }

    public function test_keuangan_can_view_refund_approval_queue(): void
    {
        $this->assertTrue(
            Gate::forUser(
                $this->userWithRole('KEUANGAN')
            )->allows(
                'viewAny',
                TicketRefund::class
            )
        );
    }

    public function test_admin_cannot_view_refund_approval_queue(): void
    {
        $this->assertFalse(
            Gate::forUser(
                $this->userWithRole('ADMIN')
            )->allows(
                'viewAny',
                TicketRefund::class
            )
        );
    }

    public function test_keuangan_can_approve_pending_refund(): void
    {
        $this->assertTrue(
            Gate::forUser(
                $this->userWithRole('KEUANGAN')
            )->allows(
                'approve',
                $this->refund('PENDING')
            )
        );
    }

    public function test_keuangan_cannot_approve_processed_refund(): void
    {
        foreach (['APPROVED', 'REJECTED'] as $status) {
            $this->assertFalse(
                Gate::forUser(
                    $this->userWithRole('KEUANGAN')
                )->allows(
                    'approve',
                    $this->refund($status)
                )
            );
        }
    }

    public function test_admin_cannot_approve_pending_refund(): void
    {
        $this->assertFalse(
            Gate::forUser(
                $this->userWithRole('ADMIN')
            )->allows(
                'approve',
                $this->refund('PENDING')
            )
        );
    }
}
