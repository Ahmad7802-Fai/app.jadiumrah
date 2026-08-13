<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Booking\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\Bookings\BookingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    protected BookingService $service;

    public function __construct(BookingService $service)
    {
        $this->service = $service;

        // semua endpoint harus login
        $this->middleware('auth:sanctum');
    }

    /*
    |--------------------------------------------------------------------------
    | LIST BOOKINGS
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Booking::query()
            ->select([
                'id',
                'booking_code',
                'paket_id',
                'paket_departure_id',
                'user_id',
                'agent_id',
                'branch_id',
                'status',
                'total_amount',
                'paid_amount',
                'created_at'
            ])
            ->with([
                'paket:id,name',
                'departure:id,paket_id,departure_date',
                'jamaahs:id,nama_lengkap'
            ])
            ->latest();

        /*
        |--------------------------------------------------------------------------
        | ROLE FILTER
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole(['SUPERADMIN','ADMIN_PUSAT'])) {
            // full access
        }

        elseif ($user->hasRole('ADMIN_CABANG')) {

            $query->where('branch_id', $user->branch_id);
        }

        elseif ($user->hasRole('AGENT')) {

            $query->where(function ($q) use ($user) {

                $q->where('agent_id', $user->id)
                  ->orWhere('created_by', $user->id);

            });
        }

        elseif ($user->hasRole('JAMAAH')) {

            $query->where('user_id', $user->id);
        }

        else {

            $query->whereRaw('1=0');
        }

        /*
        |--------------------------------------------------------------------------
        | FILTERS
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('departure_id')) {
            $query->where('paket_departure_id', $request->departure_id);
        }

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('booking_code', 'like', "%$search%")
                  ->orWhere('invoice_number', 'like', "%$search%");

            });
        }

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $perPage = min($request->integer('per_page', 15), 100);

        $bookings = $query->paginate($perPage);

        /*
        |--------------------------------------------------------------------------
        | STATS
        |--------------------------------------------------------------------------
        */

        $statsQuery = Booking::query();

        if ($user->hasRole('ADMIN_CABANG')) {

            $statsQuery->where('branch_id', $user->branch_id);

        }

        elseif ($user->hasRole('AGENT')) {

            $statsQuery->where(function ($q) use ($user) {

                $q->where('agent_id', $user->id)
                  ->orWhere('created_by', $user->id);

            });

        }

        elseif ($user->hasRole('JAMAAH')) {

            $statsQuery->where('user_id', $user->id);
        }

        $stats = $statsQuery
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status='waiting_payment' THEN 1 ELSE 0 END) as waiting_payment,
                SUM(CASE WHEN status='partial_paid' THEN 1 ELSE 0 END) as partial_paid,
                SUM(CASE WHEN status='confirmed' THEN 1 ELSE 0 END) as confirmed
            ")
            ->first();

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'data' => BookingResource::collection($bookings->items()),
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total(),
            ],
            'stats' => $stats
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE BOOKING
    |--------------------------------------------------------------------------
    */

    public function store(StoreBookingRequest $request): JsonResponse
    {
        $this->authorize('create', Booking::class);

        try {

            $booking = $this->service->create(
                $request->validated(),
                $request->user()
            );

            return response()->json([
                'success' => true,
                'data' => new BookingResource($booking)
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW BOOKING
    |--------------------------------------------------------------------------
    */

    public function show(Request $request, Booking $booking)
    {
        $this->authorize('view', $booking);

        $booking->load([
            'paket',
            'departure',
            'jamaahs',
            'payments'
        ]);

        return new BookingResource($booking);
    }

    /*
    |--------------------------------------------------------------------------
    | CONFIRM BOOKING
    |--------------------------------------------------------------------------
    */

    public function confirm(Request $request, Booking $booking)
    {
        $this->authorize('approve', $booking);

        try {

            $booking = $this->service->confirm($booking);

            return response()->json([
                'success' => true,
                'data' => new BookingResource($booking)
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL BOOKING
    |--------------------------------------------------------------------------
    */

    public function cancel(Request $request, Booking $booking)
    {
        $this->authorize('delete', $booking);

        try {

            $booking = $this->service->cancel($booking);

            return response()->json([
                'success' => true,
                'data' => new BookingResource($booking)
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}