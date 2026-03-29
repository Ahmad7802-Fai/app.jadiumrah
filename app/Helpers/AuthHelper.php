<?php

namespace App\Helpers;

class AuthHelper
{
    // ===============================
    // 🔐 CREATE COOKIE
    // ===============================
    public static function make(string $token)
    {
        return cookie(
            'token',
            $token,
            60 * 24,
            '/',
            config('session.domain', '.jadiumrah.cloud'),
            true,
            true,
            false,
            'None'
        );
    }

    // ===============================
    // 🚪 CLEAR COOKIE
    // ===============================
    public static function forget()
    {
        return cookie(
            'token',
            '',
            -1,
            '/',
            config('session.domain', '.jadiumrah.cloud'),
            true,
            true,
            false,
            'None'
        );
    }
}