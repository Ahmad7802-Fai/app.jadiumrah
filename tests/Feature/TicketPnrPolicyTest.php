<?php

namespace Tests\Feature;

use App\Models\TicketPnr;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class TicketPnrPolicyTest extends TestCase
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

    private function pnrWithStatus(string $status): TicketPnr
    {
        $pnr = new TicketPnr();

        $pnr->forceFill([
            'status' => $status,
        ]);

        return $pnr;
    }

    public function test_keuangan_can_view_pnr(): void
    {
        $user = $this->userWithRole('KEUANGAN');
        $pnr = $this->pnrWithStatus('ON_FLOW');

        $this->assertTrue(
            Gate::forUser($user)->allows('view', $pnr)
        );
    }

    public function test_keuangan_can_create_pnr(): void
    {
        $user = $this->userWithRole('KEUANGAN');

        $this->assertTrue(
            Gate::forUser($user)->allows('create', TicketPnr::class)
        );
    }

    public function test_admin_cannot_create_pnr(): void
    {
        $user = $this->userWithRole('ADMIN');

        $this->assertFalse(
            Gate::forUser($user)->allows('create', TicketPnr::class)
        );
    }

    public function test_operator_cannot_update_on_flow_pnr(): void
    {
        $user = $this->userWithRole('OPERATOR');
        $pnr = $this->pnrWithStatus('ON_FLOW');

        $this->assertFalse(
            Gate::forUser($user)->allows('update', $pnr)
        );
    }

    public function test_keuangan_can_update_on_flow_pnr(): void
    {
        $user = $this->userWithRole('KEUANGAN');
        $pnr = $this->pnrWithStatus('ON_FLOW');

        $this->assertTrue(
            Gate::forUser($user)->allows('update', $pnr)
        );
    }

    public function test_keuangan_cannot_update_issued_pnr(): void
    {
        $user = $this->userWithRole('KEUANGAN');
        $pnr = $this->pnrWithStatus('ISSUED');

        $this->assertFalse(
            Gate::forUser($user)->allows('update', $pnr)
        );
    }

    public function test_keuangan_can_confirm_on_flow_pnr(): void
    {
        $user = $this->userWithRole('KEUANGAN');
        $pnr = $this->pnrWithStatus('ON_FLOW');

        $this->assertTrue(
            Gate::forUser($user)->allows('confirm', $pnr)
        );
    }

    public function test_keuangan_cannot_confirm_non_on_flow_pnr(): void
    {
        $user = $this->userWithRole('KEUANGAN');

        foreach (['CONFIRMED', 'ISSUED'] as $status) {
            $pnr = $this->pnrWithStatus($status);

            $this->assertFalse(
                Gate::forUser($user)->allows('confirm', $pnr),
                "KEUANGAN tidak boleh confirm PNR status {$status}."
            );
        }
    }
}
