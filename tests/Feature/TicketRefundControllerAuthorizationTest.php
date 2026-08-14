<?php

namespace Tests\Feature;

use App\Http\Controllers\Ticketing\TicketRefundApprovalController;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Tests\TestCase;

class TicketRefundControllerAuthorizationTest extends TestCase
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

    public function test_approval_index_requires_view_any_policy(): void
    {
        $this->actingAsOperator();

        $this->expectException(
            AuthorizationException::class
        );

        (new TicketRefundApprovalController())->index();
    }
}
