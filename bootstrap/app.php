<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))

    /*
    |--------------------------------------------------------------------------
    | ROUTING
    |--------------------------------------------------------------------------
    */
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    /*
    |--------------------------------------------------------------------------
    | MIDDLEWARE
    |--------------------------------------------------------------------------
    */
    ->withMiddleware(function (Middleware $middleware): void {

        /*
        |--------------------------------------------------------------------------
        | API MIDDLEWARE
        |--------------------------------------------------------------------------
        | Token mode → tidak pakai EnsureFrontendRequestsAreStateful
        |--------------------------------------------------------------------------
        */
        $middleware->api([
            \App\Http\Middleware\InjectSanctumToken::class,
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | GLOBAL MIDDLEWARE
        |--------------------------------------------------------------------------
        */
        $middleware->append(
            \Illuminate\Http\Middleware\HandleCors::class
        );

        /*
        |--------------------------------------------------------------------------
        | ROUTE ALIAS
        |--------------------------------------------------------------------------
        */
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })

    /*
    |--------------------------------------------------------------------------
    | EXCEPTIONS
    |--------------------------------------------------------------------------
    */
    ->withExceptions(function (Exceptions $exceptions): void {

        /*
        |--------------------------------------------------------------------------
        | VALIDATION ERROR
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (ValidationException $e, $request) {
            if (!$request->expectsJson()) {
                return null;
            }

            $errors = [];

            foreach ($e->errors() as $field => $messages) {
                $errors[$field] = [
                    'message' => $messages[0] ?? 'Input tidak valid.',
                    'messages' => array_values($messages),
                ];
            }

            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $errors,
                'meta' => [
                    'error_type' => 'validation_error',
                    'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        /*
        |--------------------------------------------------------------------------
        | MODEL NOT FOUND
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if (!$request->expectsJson()) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.',
                'errors' => [],
                'meta' => [
                    'error_type' => 'model_not_found',
                    'status_code' => Response::HTTP_NOT_FOUND,
                ],
            ], Response::HTTP_NOT_FOUND);
        });

        /*
        |--------------------------------------------------------------------------
        | AUTHENTICATION ERROR
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (AuthenticationException $e, $request) {
            if (!$request->expectsJson()) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => [],
                'meta' => [
                    'error_type' => 'authentication_error',
                    'status_code' => Response::HTTP_UNAUTHORIZED,
                ],
            ], Response::HTTP_UNAUTHORIZED);
        });

        /*
        |--------------------------------------------------------------------------
        | AUTHORIZATION / FORBIDDEN
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (AuthorizationException $e, $request) {
            if (!$request->expectsJson()) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengakses resource ini.',
                'errors' => [],
                'meta' => [
                    'error_type' => 'forbidden',
                    'status_code' => Response::HTTP_FORBIDDEN,
                ],
            ], Response::HTTP_FORBIDDEN);
        });

        /*
        |--------------------------------------------------------------------------
        | THROTTLE ERROR
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (ThrottleRequestsException $e, $request) {
            if (!$request->expectsJson()) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak permintaan. Silakan coba lagi beberapa saat.',
                'errors' => [],
                'meta' => [
                    'error_type' => 'throttle_error',
                    'status_code' => Response::HTTP_TOO_MANY_REQUESTS,
                ],
            ], Response::HTTP_TOO_MANY_REQUESTS);
        });

        /*
        |--------------------------------------------------------------------------
        | ROUTE NOT FOUND
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if (!$request->expectsJson()) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Endpoint tidak ditemukan.',
                'errors' => [],
                'meta' => [
                    'error_type' => 'route_not_found',
                    'status_code' => Response::HTTP_NOT_FOUND,
                ],
            ], Response::HTTP_NOT_FOUND);
        });

        /*
        |--------------------------------------------------------------------------
        | FALLBACK SERVER ERROR
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (\Throwable $e, $request) {
            if (!$request->expectsJson()) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => config('app.debug')
                    ? $e->getMessage()
                    : 'Terjadi kesalahan pada server.',
                'errors' => [],
                'meta' => [
                    'error_type' => 'server_error',
                    'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        });

    })

    ->create();