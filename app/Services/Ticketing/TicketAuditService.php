<?php

namespace App\Services\Ticketing;

use App\Models\TicketAuditLog;
use Illuminate\Support\Facades\Auth;
use Throwable;

class TicketAuditService
{
    public static function log(
        string $entityType,
        int $entityId,
        string $action,
        ?array $before = null,
        ?array $after = null
    ): void {
        try {
            $user = Auth::user();

            TicketAuditLog::create([
                'entity_type' => $entityType,
                'entity_id'   => $entityId,
                'action'      => $action,

                'before'      => $before,
                'after'       => $after,

                // 👤 ACTOR
                'actor_id'    => $user?->id,
                'actor_role'  => $user
                    ? (method_exists($user, 'getRoleName')
                        ? $user->getRoleName()
                        : ($user->role ?? 'UNKNOWN'))
                    : 'SYSTEM',

                // 🌐 CONTEXT
                'ip_address'  => request()?->ip(),
                'user_agent'  => request()?->userAgent(),

                // ⏱️ TIMESTAMP MANUAL (KARENA timestamps=false)
                'created_at'  => now(),
            ]);
        } catch (Throwable $e) {
            /**
             * ❌ JANGAN throw
             * ❌ JANGAN rollback bisnis utama
             * ✅ cukup log ke laravel log
             */
            report($e);
        }
    }
}
