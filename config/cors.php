 <?php

return [

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'login',
        'logout',
    ],

    'allowed_methods' => ['*'],

    // 🔥 WAJIB: specific domain (NO '*')
    'allowed_origins' => [
        'http://localhost:3000',
        'https://jadiumrah.cloud',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // 🔥 WAJIB TRUE untuk Sanctum
    'supports_credentials' => true,

];