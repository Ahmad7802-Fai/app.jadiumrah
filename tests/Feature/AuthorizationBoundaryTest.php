<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Tests\TestCase;

class AuthorizationBoundaryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Gate::define(
            'authorization-boundary-probe',
            fn (User $user): bool => false
        );
    }

    protected function tearDown(): void
    {
        Auth::guard()->forgetUser();

        parent::tearDown();
    }

    private function userWithRole(string $role): User
    {
        $user = new User();

        $user->forceFill([
            'role' => $role,
            'is_active' => true,
        ]);

        return $user;
    }

    private function roleMiddlewareStatus(
        string $userRole,
        string ...$allowedRoles
    ): int {
        Auth::setUser($this->userWithRole($userRole));

        $request = Request::create('/authorization-probe', 'GET');

        try {
            $response = (new CheckRole())->handle(
                $request,
                fn () => response('', 204),
                ...$allowedRoles
            );

            return $response->getStatusCode();
        } catch (HttpExceptionInterface $exception) {
            return $exception->getStatusCode();
        }
    }

    public function test_superadmin_bypasses_role_middleware(): void
    {
        $this->assertSame(
            204,
            $this->roleMiddlewareStatus('SUPERADMIN', 'ADMIN')
        );
    }

    public function test_operator_does_not_bypass_admin_role_middleware(): void
    {
        $this->assertSame(
            403,
            $this->roleMiddlewareStatus('OPERATOR', 'ADMIN')
        );
    }

    public function test_operator_can_access_operator_role_middleware(): void
    {
        $this->assertSame(
            204,
            $this->roleMiddlewareStatus('OPERATOR', 'OPERATOR')
        );
    }

    public function test_keuangan_does_not_bypass_admin_role_middleware(): void
    {
        $this->assertSame(
            403,
            $this->roleMiddlewareStatus('KEUANGAN', 'ADMIN')
        );
    }

    public function test_superadmin_bypasses_gate_globally(): void
    {
        $user = $this->userWithRole('SUPERADMIN');

        $this->assertTrue(
            Gate::forUser($user)->allows('authorization-boundary-probe')
        );
    }

    public function test_operator_does_not_bypass_gate_globally(): void
    {
        $user = $this->userWithRole('OPERATOR');

        $this->assertFalse(
            Gate::forUser($user)->allows('authorization-boundary-probe')
        );
    }

    public function test_keuangan_does_not_bypass_gate_globally(): void
    {
        $user = $this->userWithRole('KEUANGAN');

        $this->assertFalse(
            Gate::forUser($user)->allows('authorization-boundary-probe')
        );
    }
}
