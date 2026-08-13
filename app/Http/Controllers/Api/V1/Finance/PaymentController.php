<?php

namespace App\Http\Controllers\Api\V1\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Finance\PaymentActionRequest;
use App\Http\Requests\Api\V1\Finance\StorePaymentRequest;
use App\Http\Resources\Api\V1\Finance\PaymentResource;
use App\Models\Payment;
use App\Models\Booking;
use App\Services\Finance\PaymentService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);

        $query = Payment::query()
            ->with($this->relations())
            ->latest('id');

        $this->applyRoleScope($query, $user);
        $this->applyFilters($query, $request);

        $payments = $query->paginate($perPage)->appends($request->query());

        $statsQuery = Payment::query();
        $this->applyRoleScope($statsQuery, $user);
        $this->applyFilters($statsQuery, $request, includeBookingEffectiveStatus: false);

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'pending' => (clone $statsQuery)->where('status', 'pending')->count(),
            'paid' => (clone $statsQuery)->where('status', 'paid')->count(),
            'failed' => (clone $statsQuery)->where('status', 'failed')->count(),
            'cancelled' => (clone $statsQuery)->where('status', 'cancelled')->count(),
        ];

        $effectiveStatsBase = Payment::query();
        $this->applyRoleScope($effectiveStatsBase, $user);
        $this->applyFilters($effectiveStatsBase, $request, includeBookingEffectiveStatus: false);

        $effectiveStats = [
            'pending_active' => (clone $effectiveStatsBase)
                ->where('status', 'pending')
                ->whereHas('booking', function ($q) {
                    $q->where(function ($b) {
                        $b->whereNull('expired_at')
                            ->orWhere('expired_at', '>', now());
                    });
                })
                ->count(),

            'pending_expired' => (clone $effectiveStatsBase)
                ->where('status', 'pending')
                ->whereHas('booking', function ($q) {
                    $q->whereNotNull('expired_at')
                        ->where('expired_at', '<=', now());
                })
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Daftar payment berhasil diambil.',
            'data' => PaymentResource::collection($payments),
            'meta' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
                'from' => $payments->firstItem(),
                'to' => $payments->lastItem(),
                'count' => $payments->count(),
                'has_more' => $payments->hasMorePages(),
                'empty' => $payments->isEmpty(),
            ],
            'stats' => $stats,
            'effective_stats' => $effectiveStats,
            'filters' => [
                'status' => $request->input('status') ?: null,
                'booking_id' => $request->input('booking_id') ?: null,
                'booking_effective_status' => $request->input('booking_effective_status') ?: null,
                'search' => $request->filled('search')
                    ? trim((string) $request->input('search'))
                    : null,
            ],
        ]);
    }

    public function store(StorePaymentRequest $request): JsonResponse
    {
        $payment = $this->service->create($request->validated());
        $payment->load($this->relations());

        return response()->json([
            'success' => true,
            'message' => 'Payment berhasil dibuat.',
            'data' => new PaymentResource($payment),
        ], 201);
    }

    public function show(Request $request, Payment $payment): JsonResponse
    {
        $this->authorizeView($payment, $request->user());

        $payment->load($this->relations());

        return response()->json([
            'success' => true,
            'message' => 'Detail payment berhasil diambil.',
            'data' => new PaymentResource($payment),
        ]);
    }

    public function update(StorePaymentRequest $request, Payment $payment): JsonResponse
    {
        $this->authorizeManage($request->user());

        $payment = $this->service->update($payment, $request->validated());
        $payment->load($this->relations());

        return response()->json([
            'success' => true,
            'message' => 'Payment berhasil diperbarui.',
            'data' => new PaymentResource($payment),
        ]);
    }

    public function approve(Request $request, Payment $payment): JsonResponse
    {
        $this->authorizeManage($request->user());

        $payment = $this->service->approve($payment);
        $payment->load($this->relations());

        return response()->json([
            'success' => true,
            'message' => 'Payment berhasil disetujui.',
            'data' => new PaymentResource($payment),
        ]);
    }

    public function reject(PaymentActionRequest $request, Payment $payment): JsonResponse
    {
        $this->authorizeManage($request->user());

        $payment = $this->service->reject(
            $payment,
            $request->input('reason')
        );

        $payment->load($this->relations());

        return response()->json([
            'success' => true,
            'message' => 'Payment berhasil ditolak.',
            'data' => new PaymentResource($payment),
        ]);
    }

    public function cancel(PaymentActionRequest $request, Payment $payment): JsonResponse
    {
        $this->authorizeManage($request->user());

        $payment = $this->service->cancel(
            $payment,
            $request->input('reason')
        );

        $payment->load($this->relations());

        return response()->json([
            'success' => true,
            'message' => 'Payment berhasil dibatalkan.',
            'data' => new PaymentResource($payment),
        ]);
    }

    public function destroy(Request $request, Payment $payment): JsonResponse
    {
        $this->authorizeManage($request->user());

        $this->service->delete($payment);

        return response()->json([
            'success' => true,
            'message' => 'Payment berhasil dihapus.',
        ]);
    }

    protected function relations(): array
    {
        return [
            'booking:id,booking_code,invoice_number,status,total_amount,paid_amount,expired_at,paket_id,paket_departure_id,agent_id,created_by,user_id',
            'booking.paket:id,name,slug',
            'booking.departure:id,paket_id,departure_date,return_date',
            'jamaah:id,nama_lengkap',
        ];
    }

    protected function applyRoleScope(Builder $query, $user): void
    {
        if ($user->hasRole(['SUPERADMIN', 'ADMIN_PUSAT'])) {
            return;
        }

        if ($user->hasRole('ADMIN_CABANG')) {
            $query->where('branch_id', $user->branch_id);
            return;
        }

        if ($user->hasRole('AGENT')) {
            $query->whereHas('booking', function ($q) use ($user) {
                $q->where('agent_id', $user->id)
                    ->orWhere('created_by', $user->id)
                    ->orWhere('user_id', $user->id);
            });
            return;
        }

        if ($user->hasRole('JAMAAH')) {
            $query->whereHas('booking', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
            return;
        }

        $query->whereRaw('1 = 0');
    }

    protected function applyFilters(
        Builder $query,
        Request $request,
        bool $includeBookingEffectiveStatus = true
    ): void {
        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('booking_id')) {
            $query->where('booking_id', (int) $request->input('booking_id'));
        }

        if ($includeBookingEffectiveStatus && $request->filled('booking_effective_status')) {
            $effectiveStatus = trim((string) $request->input('booking_effective_status'));

            if ($effectiveStatus === 'expired') {
                $query->whereHas('booking', function ($q) {
                    $q->whereNotNull('expired_at')
                        ->where('expired_at', '<=', now());
                });
            }

            if (in_array($effectiveStatus, ['active', 'not_expired'], true)) {
                $query->whereHas('booking', function ($q) {
                    $q->where(function ($b) {
                        $b->whereNull('expired_at')
                            ->orWhere('expired_at', '>', now());
                    });
                });
            }
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('payment_code', 'like', "%{$search}%")
                    ->orWhere('receipt_number', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('booking', function ($b) use ($search) {
                        $b->where('booking_code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('jamaah', function ($j) use ($search) {
                        $j->where('nama_lengkap', 'like', "%{$search}%");
                    });
            });
        }
    }

    protected function authorizeView(Payment $payment, $user): void
    {
        $payment->loadMissing('booking');

        $allowed = false;

        if ($user->hasRole(['SUPERADMIN', 'ADMIN_PUSAT'])) {
            $allowed = true;
        } elseif ($user->hasRole('ADMIN_CABANG')) {
            $allowed = (int) $payment->branch_id === (int) $user->branch_id;
        } elseif ($user->hasRole('AGENT')) {
            $booking = $payment->booking;
            $allowed = $booking && (
                (int) $booking->agent_id === (int) $user->id ||
                (int) $booking->created_by === (int) $user->id ||
                (int) $booking->user_id === (int) $user->id
            );
        } elseif ($user->hasRole('JAMAAH')) {
            $booking = $payment->booking;
            $allowed = $booking && (int) $booking->user_id === (int) $user->id;
        }

        abort_unless($allowed, 403, 'Anda tidak memiliki akses ke payment ini.');
    }

    protected function authorizeManage($user): void
    {
        abort_unless(
            $user->hasRole(['SUPERADMIN', 'ADMIN_PUSAT', 'ADMIN_CABANG']),
            403,
            'Anda tidak memiliki izin untuk mengelola payment.'
        );
    }

    public function byBooking(Request $request, Booking $booking): JsonResponse
    {
        $this->authorizeViewBooking($booking, $request->user());

        $perPage = min(max((int) $request->input('per_page', 10), 1), 50);

        $payments = $booking->payments()
            ->with($this->relations())
            ->latest()
            ->paginate($perPage)
            ->appends($request->query());

        return response()->json([
            'success' => true,
            'message' => 'Daftar payment booking berhasil diambil.',
            'data' => PaymentResource::collection($payments),

            'meta' => [
                'current_page' => $payments->currentPage(),
                'last_page'    => $payments->lastPage(),
                'per_page'     => $payments->perPage(),
                'total'        => $payments->total(),
                'from'         => $payments->firstItem(),
                'to'           => $payments->lastItem(),
                'count'        => $payments->count(),
                'has_more'     => $payments->hasMorePages(),
                'empty'        => $payments->isEmpty(),
            ],
        ]);
    }

   protected function authorizeViewBooking(Booking $booking, $user): void
    {
        $allowed = match (true) {

            $user->hasRole(['SUPERADMIN', 'ADMIN_PUSAT']) => true,

            $user->hasRole('ADMIN_CABANG') =>
                (int) $booking->branch_id === (int) $user->branch_id,

            $user->hasRole('AGENT') =>
                (int) $booking->agent_id === (int) $user->id ||
                (int) $booking->created_by === (int) $user->id ||
                (int) $booking->user_id === (int) $user->id,

            $user->hasRole('JAMAAH') =>
                (int) $booking->user_id === (int) $user->id,

            default => false,
        };

        abort_unless($allowed, 403, 'Tidak punya akses booking ini.');
    }

    public function storeByBooking(StorePaymentRequest $request, Booking $booking): JsonResponse
    {
        $this->authorizeViewBooking($booking, $request->user());

        // 🔒 EXTRA GUARD (defensive, walau sudah di Request)
        if ($booking->isExpired()) {
            abort(422, 'Booking sudah expired.');
        }

        if (in_array($booking->status, ['cancelled', 'confirmed'], true)) {
            abort(422, 'Booking tidak bisa dibayar.');
        }

        // 🔥 CLEAN MERGE
        $data = array_merge(
            $request->validated(),
            [
                'booking_id' => $booking->id,
                'channel'    => $request->input('channel', 'website'),
            ]
        );

        $payment = $this->service->create($data);
        $payment->load($this->relations());

        return response()->json([
            'success' => true,
            'message' => 'Payment berhasil dibuat.',
            'data' => new PaymentResource($payment),
        ], 201);
    }

}