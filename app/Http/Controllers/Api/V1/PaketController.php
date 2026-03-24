<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaketResource;
use App\Http\Resources\PaketDetailResource;
use App\Services\Pakets\PaketQueryService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaketController extends Controller
{
    public function __construct(
        protected PaketQueryService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | PUBLIC LISTING
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'departure_city',
            'min_price',
            'max_price',
            'departure_date'
        ]);

        $pakets = $this->service->publicList($filters);

        return PaketResource::collection($pakets);
    }

    /*
    |--------------------------------------------------------------------------
    | PUBLIC DETAIL
    |--------------------------------------------------------------------------
    */

    public function show(string $slug)
    {
        $paket = $this->service->publicDetail($slug);

        if (!$paket) {
            abort(Response::HTTP_NOT_FOUND,'Paket tidak ditemukan');
        }

        return new PaketDetailResource($paket);
    }
}