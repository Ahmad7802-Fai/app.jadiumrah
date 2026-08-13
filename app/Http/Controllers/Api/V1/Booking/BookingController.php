<?php

namespace App\Http\Controllers\Api\V1\Booking;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Booking\StoreBookingRequest;
use App\Http\Resources\Api\V1\Booking\BookingResource;
use App\Models\Booking;
use App\Services\Bookings\BookingService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $filters = [
            'status'       => $request->input('status'),
            'departure_id' => $request->input('departure_id'),
            'search'       => trim((string) $request->input('search')),
        ];

        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);

        $query = Booking::query()
        ->select([
            'id',
            'booking_code',
            'invoice_number',
            'paket_id',
            'paket_departure_id',
            'user_id',
            'agent_id',
            'branch_id',
            'created_by',
            'status',
            'room_type',
            'qty',
            'price_per_person_snapshot',
            'total_amount',
            'expired_at',
            'created_at',
        ])
        ->with([
            'paket:id,name,slug',
            'departure:id,paket_id,departure_date,return_date',
            'jamaahs:id,nama_lengkap',
        ])
        ->withSum([
            'payments as paid_total' => fn ($q) =>
                $q->where('status', 'paid')
        ], 'amount')
        ->latest('id');

        $this->scopeByRole($query, $user);
        $this->applyFilters($query, $filters);

        $bookings = $query->paginate($perPage)->appends($request->query());

        $stats = $this->buildStats($user, $filters);

        return response()->json([
            'success' => true,
            'message' => 'Daftar booking berhasil diambil.',
            'data'    => BookingResource::collection($bookings),

            'meta' => [
                'current_page' => $bookings->currentPage(),
                'last_page'    => $bookings->lastPage(),
                'per_page'     => $bookings->perPage(),
                'total'        => $bookings->total(),
                'from'         => $bookings->firstItem(),
                'to'           => $bookings->lastItem(),
                'count'        => $bookings->count(),
                'has_more'     => $bookings->hasMorePages(),
                'empty'        => $bookings->isEmpty(),
            ],

            'stats'   => $stats,
            'filters' => $filters,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
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

            /*
            |--------------------------------------------------------------------------
            | 🔥 LOAD RELATIONS + AGGREGATE (WAJIB)
            |--------------------------------------------------------------------------
            */
            $booking->load([
                'paket:id,name,slug',
                'departure:id,paket_id,departure_date,return_date',
                'jamaahs:id,nama_lengkap',
            ])->loadSum([
                'payments as paid_total' => fn ($q) =>
                    $q->where('status', 'paid')
            ], 'amount');

            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil dibuat.',
                'data'    => new BookingResource($booking),
            ], 201);

        } catch (\Throwable $e) {

            Log::error('BOOKING_CREATE_ERROR', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */
    public function show(Request $request, Booking $booking): JsonResponse
    {
        $this->authorize('view', $booking);

        $booking->load([
            'paket:id,name,slug',
            'departure:id,paket_id,departure_date,return_date',
            'jamaahs:id,nama_lengkap',
            'payments:id,booking_id,amount,status,paid_at,method',
        ])->loadSum([
            'payments as paid_total' => fn ($q) =>
                $q->where('status', 'paid')
        ], 'amount');

        return response()->json([
            'success' => true,
            'message' => 'Detail booking berhasil diambil.',
            'data'    => new BookingResource($booking),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CONFIRM
    |--------------------------------------------------------------------------
    */
    public function confirm(Request $request, Booking $booking): JsonResponse
    {
        $this->authorize('approve', $booking);

        try {
            $booking = $this->service->confirm($booking);

            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil dikonfirmasi.',
                'data'    => new BookingResource($booking),
            ]);

        } catch (\Throwable $e) {

            Log::error('BOOKING_CONFIRM_ERROR', [
                'booking_id' => $booking->id,
                'message'    => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal konfirmasi booking.',
            ], 422);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL
    |--------------------------------------------------------------------------
    */
    public function cancel(Request $request, Booking $booking): JsonResponse
    {
        $this->authorize('delete', $booking);

        try {
            $booking = $this->service->cancel($booking);

            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil dibatalkan.',
                'data'    => new BookingResource($booking),
            ]);

        } catch (\Throwable $e) {

            Log::error('BOOKING_CANCEL_ERROR', [
                'booking_id' => $booking->id,
                'message'    => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan booking.',
            ], 422);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    protected function scopeByRole(Builder $query, $user): void
    {
        if ($user->hasRole(['SUPERADMIN', 'ADMIN_PUSAT'])) return;

        if ($user->hasRole('ADMIN_CABANG')) {
            $query->where('branch_id', $user->branch_id);
            return;
        }

        if ($user->hasRole('AGENT')) {
            $query->where(function ($q) use ($user) {
                $q->where('agent_id', $user->id)
                  ->orWhere('created_by', $user->id);
            });
            return;
        }

        if ($user->hasRole('JAMAAH')) {
            $query->where('user_id', $user->id);
            return;
        }

        $query->whereRaw('1=0');
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['departure_id'])) {
            $query->where('paket_departure_id', $filters['departure_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                  ->orWhere('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('paket', fn ($q) =>
                      $q->where('name', 'like', "%{$search}%")
                  )
                  ->orWhereHas('jamaahs', fn ($q) =>
                      $q->where('nama_lengkap', 'like', "%{$search}%")
                  );
            });
        }

        $this->applyStatusFilter($query, $filters['status'] ?? null);
    }

    protected function applyStatusFilter(Builder $query, ?string $status): void
    {
        if (!$status) return;

        $now = now();

        if ($status === 'expired') {
            $query->whereIn('status', ['waiting_payment', 'partial_paid'])
                ->whereNotNull('expired_at')
                ->where('expired_at', '<', $now);
            return;
        }

        if (in_array($status, ['waiting_payment', 'partial_paid'])) {
            $query->where('status', $status)
                ->where(fn ($q) =>
                    $q->whereNull('expired_at')
                      ->orWhere('expired_at', '>=', $now)
                );
            return;
        }

        $query->where('status', $status);
    }

    /*
    |--------------------------------------------------------------------------
    | STATS
    |--------------------------------------------------------------------------
    */
    protected function buildStats($user, array $filters): array
    {
        $query = Booking::query();
        $this->scopeByRole($query, $user);
        $this->applyFilters($query, $filters);

        $now = now();

        return [
            'total' => (clone $query)->count(),

            'confirmed' => (clone $query)
                ->where('status', 'confirmed')->count(),

            'cancelled' => (clone $query)
                ->where('status', 'cancelled')->count(),

            'expired' => (clone $query)
                ->whereIn('status', ['waiting_payment', 'partial_paid'])
                ->whereNotNull('expired_at')
                ->where('expired_at', '<', $now)
                ->count(),

            'waiting_payment' => (clone $query)
                ->where('status', 'waiting_payment')
                ->where(fn ($q) =>
                    $q->whereNull('expired_at')
                      ->orWhere('expired_at', '>=', $now)
                )->count(),

            'partial_paid' => (clone $query)
                ->where('status', 'partial_paid')
                ->where(fn ($q) =>
                    $q->whereNull('expired_at')
                      ->orWhere('expired_at', '>=', $now)
                )->count(),
        ];
    }
}