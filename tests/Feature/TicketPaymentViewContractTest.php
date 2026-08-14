<?php

namespace Tests\Feature;

use Tests\TestCase;

class TicketPaymentViewContractTest extends TestCase
{
    public function test_legacy_standalone_payment_form_is_not_present(): void
    {
        $this->assertFileDoesNotExist(
            resource_path(
                'views/ticketing/payment/_form.blade.php'
            ),
            'Standalone payment form lama harus dihapus karena tidak memiliki caller.'
        );
    }

    public function test_active_invoice_payment_form_uses_current_contract(): void
    {
        $source = file_get_contents(
            resource_path(
                'views/ticketing/invoice/show.blade.php'
            )
        );

        $this->assertStringContainsString(
            "route('ticketing.payment.store', \$invoice)",
            $source
        );

        $this->assertStringContainsString(
            'name="receipt"',
            $source
        );

        $this->assertStringContainsString(
            '<option value="VA">Virtual Account</option>',
            $source
        );

        $this->assertStringNotContainsString(
            'VIRTUAL_ACCOUNT',
            $source
        );

        $this->assertStringNotContainsString(
            'name="payment_date"',
            $source
        );
    }
}
