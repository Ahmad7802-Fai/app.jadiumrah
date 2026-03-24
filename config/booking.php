<?php

return [

    /*
    |--------------------------------------------------------------------------
    | BOOKING EXPIRATION
    |--------------------------------------------------------------------------
    */

    'expire_hours' => (int) env('BOOKING_EXPIRE_HOURS', 24),

    'channel_expire_hours' => [

        'website'    => (int) env('BOOKING_EXPIRE_WEBSITE', 24),
        'agent'      => (int) env('BOOKING_EXPIRE_AGENT', 24),
        'branch'     => (int) env('BOOKING_EXPIRE_BRANCH', 24),
        'flash_sale' => (int) env('BOOKING_EXPIRE_FLASH', 1),

    ],

    'expire_grace_minutes' => (int) env('BOOKING_EXPIRE_GRACE_MINUTES', 1),

    'expire_batch_size' => (int) env('BOOKING_EXPIRE_BATCH_SIZE', 100),

    'expire_queue' => env('BOOKING_EXPIRE_QUEUE', 'booking-expire'),

    /*
    |--------------------------------------------------------------------------
    | SEAT MANAGEMENT
    |--------------------------------------------------------------------------
    */

    'auto_reopen_departure' => (bool) env('BOOKING_AUTO_REOPEN', true),

    'auto_close_when_full' => (bool) env('BOOKING_AUTO_CLOSE_WHEN_FULL', true),

    /*
    |--------------------------------------------------------------------------
    | BOOKING CODE
    |--------------------------------------------------------------------------
    */

    'code' => [

        'prefix' => env('BOOKING_CODE_PREFIX', 'BOOK'),

        'pad'    => (int) env('BOOKING_CODE_PAD', 5),

        'yearly' => (bool) env('BOOKING_CODE_YEARLY', true),

    ],

    /*
    |--------------------------------------------------------------------------
    | INVOICE CODE
    |--------------------------------------------------------------------------
    */

    'invoice' => [

        'prefix' => env('BOOKING_INVOICE_PREFIX', 'INV'),

        'pad' => (int) env('BOOKING_INVOICE_PAD', 5),

        'yearly' => (bool) env('BOOKING_INVOICE_YEARLY', true),

    ],

    /*
    |--------------------------------------------------------------------------
    | RECEIPT CODE
    |--------------------------------------------------------------------------
    */

    'receipt' => [

        'prefix' => env('PAYMENT_RECEIPT_PREFIX', 'RCPT'),

        'pad' => (int) env('PAYMENT_RECEIPT_PAD', 5),

        'yearly' => (bool) env('PAYMENT_RECEIPT_YEARLY', true),

    ],

];