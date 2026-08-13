<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Session\TokenMismatchException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Inputs yang tidak pernah di-flash ke session
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register exception handling
     */
    public function register(): void
    {
        /**
         * =============================================
         * CSRF TOKEN EXPIRED (419) → REDIRECT LOGIN
         * =============================================
         */
        $this->renderable(function (TokenMismatchException $e, $request) {

            // kalau request web biasa (bukan API)
            if (! $request->expectsJson()) {
                return redirect()
                    ->route('login')
                    ->with('error', 'Session Anda sudah habis. Silakan login kembali.');
            }

            // fallback JSON
            return response()->json([
                'message' => 'Session expired'
            ], 419);
        });

        /**
         * =============================================
         * SIGNED URL EXPIRED / INVALID (WA APPROVE)
         * =============================================
         */
        $this->renderable(function (InvalidSignatureException $e, $request) {

            // khusus route WA approve
            if ($request->routeIs('topup.approve')) {
                return response()
                    ->view('wa.expired', [], 403);
            }

            return response('Link tidak valid atau sudah kadaluarsa.', 403);
        });

        // default reporter
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}


// namespace App\Exceptions;

// use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
// use Illuminate\Routing\Exceptions\InvalidSignatureException;
// use Throwable;

// class Handler extends ExceptionHandler
// {
//     /**
//      * Inputs yang tidak pernah di-flash ke session
//      */
//     protected $dontFlash = [
//         'current_password',
//         'password',
//         'password_confirmation',
//     ];

//     /**
//      * Register exception handling
//      */
//     public function register(): void
//     {
//         // default
//         $this->reportable(function (Throwable $e) {
//             //
//         });

//         /**
//          * =============================================
//          * SIGNED URL EXPIRED / INVALID (WA APPROVE)
//          * =============================================
//          */
//         $this->renderable(function (InvalidSignatureException $e, $request) {

//             // khusus route WA approve
//             if ($request->routeIs('topup.approve')) {
//                 return response()
//                     ->view('wa.expired', [], 403);
//             }

//             // fallback default
//             return response('Link tidak valid atau sudah kadaluarsa.', 403);
//         });
//     }
// }
