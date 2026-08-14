<?php

namespace Tests\Feature;

use App\Models\TicketRefund;
use Tests\TestCase;

class TicketRefundMetadataContractTest extends TestCase
{
    public function test_requester_relation_uses_refunded_by_foreign_key(): void
    {
        $refund = new TicketRefund;

        $this->assertSame(
            'refunded_by',
            $refund->requester()->getForeignKeyName()
        );
    }

    public function test_approval_status_badge_has_no_legacy_executed_state(): void
    {
        $source = file_get_contents(
            resource_path(
                'views/ticketing/refund/_status_badge.blade.php'
            )
        );

        $this->assertStringNotContainsString(
            "@case('EXECUTED')",
            $source
        );
    }
}
