<?php

namespace Tests\Feature;

use Tests\TestCase;

class TicketInvoicePdfControllerHistoricalCleanupContractTest extends TestCase
{
    private function source(): string
    {
        return file_get_contents(
            app_path('Http/Controllers/Ticketing/TicketInvoicePdfController.php')
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
                'class TicketInvoicePdfController extends Controller'
            )
        );
    }

    public function test_controller_has_single_show_method(): void
    {
        $this->assertSame(
            1,
            substr_count(
                $this->source(),
                'public function show('
            )
        );
    }

    public function test_controller_has_single_pdf_view_generation(): void
    {
        $this->assertSame(
            1,
            substr_count(
                $this->source(),
                'Pdf::loadView('
            )
        );
    }
}
