<?php

namespace App\Http\Controllers\Api\V1\Paket;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Paket\PaketIndexRequest;
use App\Http\Resources\Api\V1\Paket\PaketDetailResource;
use App\Http\Resources\Api\V1\Paket\PaketResource;
use App\Services\Pakets\PaketQueryService;
use Illuminate\Http\JsonResponse;

class PaketController extends Controller
{
    public function __construct(
        protected PaketQueryService $paketQueryService
    ) {
    }

    public function index(PaketIndexRequest $request): JsonResponse
    {
        $filters = $request->filters();
        $paginated = $this->paketQueryService->publicList($filters);

        return response()->json([
            'success' => true,
            'message' => 'Daftar paket berhasil diambil.',
            'data' => PaketResource::collection($paginated->getCollection()),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'from' => $paginated->firstItem(),
                'to' => $paginated->lastItem(),
                'count' => $paginated->count(),
                'has_more' => $paginated->hasMorePages(),
                'empty' => $paginated->isEmpty(),
            ],
            'filters' => [
                'search' => $filters['search'] ?? null,
                'departure_city' => $filters['departure_city'] ?? null,
                'min_price' => $filters['min_price'] ?? null,
                'max_price' => $filters['max_price'] ?? null,
                'departure_date' => $filters['departure_date'] ?? null,
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $paket = $this->paketQueryService->publicDetail($slug);

        return response()->json([
            'success' => true,
            'message' => 'Detail paket berhasil diambil.',
            'data' => new PaketDetailResource($paket),
        ]);
    }
}