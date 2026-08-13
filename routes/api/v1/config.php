<?php

use Illuminate\Support\Facades\Route;

Route::get('/config', function () {
    return response()->json([
        'success' => true,
        'data' => [
            'api_base' => config('app.url') . '/api/v1',

            // 🔥 FEATURE FLAG
            'features' => [
                'promo' => true,
                'maintenance' => false,
                'booking' => true,
            ],

            // 🔥 APP SETTINGS
            'app' => [
                'name' => config('app.name'),
                'env' => config('app.env'),
            ],
        ]
    ]);
});