<?php

use App\Models\SecurityLog;

if (!function_exists('security_log')) {
    function security_log(string $action, string $description = null): void
    {
        try {
            $user = auth('jamaah')->user();

            SecurityLog::create([
                'jamaah_user_id' => $user?->id,
                'action'         => $action,
                'description'    => $description,
                'ip_address'     => request()->ip(),
                'user_agent'     => substr(request()->userAgent(), 0, 500),
                'created_at'     => now(),
            ]);
        } catch (\Throwable $e) {
            // sengaja DIAM — logging tidak boleh ganggu app
        }
    }
}
