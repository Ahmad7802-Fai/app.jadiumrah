<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (ValidationException $e, $request): ?JsonResponse {
            if (!$request->expectsJson()) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $this->transformValidationErrors($e),
                'meta' => [
                    'error_type' => 'validation_error',
                    'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        $this->renderable(function (ModelNotFoundException $e, $request): ?JsonResponse {
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

        $this->renderable(function (AuthenticationException $e, $request): ?JsonResponse {
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

        $this->renderable(function (ThrottleRequestsException $e, $request): ?JsonResponse {
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
    }

    protected function transformValidationErrors(ValidationException $e): array
    {
        $errors = [];

        foreach ($e->errors() as $field => $messages) {
            $errors[$field] = [
                'message' => $messages[0] ?? 'Input tidak valid.',
                'messages' => array_values($messages),
            ];
        }

        return $errors;
    }
}