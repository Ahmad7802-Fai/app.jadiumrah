<?php

namespace App\Helpers;

class AuthHelper
{
    // ===============================
    // 🔐 CREATE COOKIE
    // ===============================
    public static function make(string $token)
    {
        $isLocal = app()->environment('local');

        return cookie(
            'token',
            $token,
            60 * 24,
            '/',
            $isLocal ? null : '.jadiumrah.cloud',
            !$isLocal, // secure hanya production
            true,
            false,
            $isLocal ? 'Lax' : 'None'
        );
    }

    // ===============================
    // 🚪 CLEAR COOKIE
    // ===============================
    public static function forget()
    {
        $isLocal = app()->environment('local');

        return cookie(
            'token',
            '',
            -1,
            '/',
            $isLocal ? null : '.jadiumrah.cloud',
            !$isLocal,
            true,
            false,
            $isLocal ? 'Lax' : 'None'
        );
    }

}