<?php

namespace Tests\Feature;

use Tests\TestCase;

class TicketPaymentControllerHistoricalCleanupContractTest extends TestCase
{
    private function source(): string
    {
        return file_get_contents(
            app_path('Http/Controllers/Ticketing/TicketPaymentController.php')
        );
    }

    public function test_controller_has_single_namespace_declaration(): void
    {
        $this->assertSame(
            1,
            substr_count(
                $this->source(),
                'namespace App\Http\Controllers\Ticketing;'
            )
        );
    }

    public function test_controller_has_single_class_declaration(): void
    {
        $this->assertSame(
            1,
            substr_count(
                $this->source(),
                'class TicketPaymentController extends Controller'
            )
        );
    }

    public function test_controller_has_single_store_method(): void
    {
        $this->assertSame(
            1,
            substr_count(
                $this->source(),
                'public function store('
            )
        );
    }

    public function test_controller_has_no_legacy_constructor_injection(): void
    {
        $this->assertStringNotContainsString(
            'public function __construct(',
            $this->source()
        );
    }
}
