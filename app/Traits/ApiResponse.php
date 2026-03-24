<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponse
{
    protected function successResponse(
        string $message = 'Berhasil.',
        mixed $data = null,
        array $meta = [],
        array $links = [],
        int $status = 200
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        if (!empty($links)) {
            $response['links'] = $links;
        }

        return response()->json($response, $status);
    }

    protected function errorResponse(
        string $message = 'Terjadi kesalahan.',
        array $errors = [],
        int $status = 400,
        array $meta = []
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $status);
    }

    protected function paginatedMeta(
        LengthAwarePaginator $paginator,
        array $filters = [],
        array $extra = []
    ): array {
        return array_merge([
            'filters' => $filters,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
                'count' => $paginator->count(),
                'has_more_pages' => $paginator->hasMorePages(),
            ],
        ], $extra);
    }

    protected function paginatedLinks(LengthAwarePaginator $paginator, string $self): array
    {
        return [
            'self' => $self,
            'first' => $paginator->url(1),
            'last' => $paginator->url($paginator->lastPage()),
            'prev' => $paginator->previousPageUrl(),
            'next' => $paginator->nextPageUrl(),
        ];
    }
}