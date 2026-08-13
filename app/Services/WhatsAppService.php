<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class WhatsAppService
{
    public function send(string $phone, string $message): bool
    {
        // ===============================
        // DEV MODE SAFE GUARD
        // ===============================
        if (!config('services.whatsapp.endpoint')) {
            Log::info('[WA MOCK]', [
                'to' => $phone,
                'message' => $message,
            ]);
            return true;
        }

        try {
            Log::info('[WA SEND ATTEMPT]', [
                'to' => $phone,
            ]);

            $response = Http::timeout(10)
                ->withToken(config('services.whatsapp.token'))
                ->post(config('services.whatsapp.endpoint'), [
                    'phone'   => $phone,
                    'message' => $message,
                ]);

            if (!$response->successful()) {
                Log::error('[WA FAILED]', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                throw new Exception('WA Gateway error');
            }

            Log::info('[WA SENT]', [
                'to' => $phone,
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('[WA EXCEPTION]', [
                'to' => $phone,
                'error' => $e->getMessage(),
            ]);

            // ❗ PENTING: JANGAN CRASH APLIKASI
            return false;
        }
    }
}
