<?php

namespace App\Http\Controllers\Api\V1\Jamaah;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Jamaah\StoreJamaahRequest;
use App\Http\Resources\Api\V1\Jamaah\JamaahResource;
use App\Models\Jamaah;
use App\Services\Jamaah\JamaahService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JamaahController extends Controller
{
    public function __construct(
        protected JamaahService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);

        $query = Jamaah::query()
            ->latest('id');

        $this->applyRoleScope($query, $user);
        $this->applyFilters($query, $request);

        $jamaahs = $query->paginate($perPage)->appends($request->query());

        $statsQuery = Jamaah::query();
        $this->applyRoleScope($statsQuery, $user);
        $this->applyFilters($statsQuery, $request);

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'pending' => (clone $statsQuery)->where('approval_status', 'pending')->count(),
            'approved' => (clone $statsQuery)->where('approval_status', 'approved')->count(),
            'rejected' => (clone $statsQuery)->where('approval_status', 'rejected')->count(),
            'active' => (clone $statsQuery)->where('is_active', true)->count(),
            'inactive' => (clone $statsQuery)->where('is_active', false)->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Daftar jamaah berhasil diambil.',
            'data' => JamaahResource::collection($jamaahs),
            'meta' => [
                'current_page' => $jamaahs->currentPage(),
                'last_page' => $jamaahs->lastPage(),
                'per_page' => $jamaahs->perPage(),
                'total' => $jamaahs->total(),
                'from' => $jamaahs->firstItem(),
                'to' => $jamaahs->lastItem(),
                'count' => $jamaahs->count(),
                'has_more' => $jamaahs->hasMorePages(),
                'empty' => $jamaahs->isEmpty(),
            ],
            'stats' => $stats,
            'filters' => [
                'approval_status' => $request->input('approval_status') ?: null,
                'source' => $request->input('source') ?: null,
                'is_active' => $request->has('is_active')
                    ? filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
                    : null,
                'search' => $request->filled('search')
                    ? trim((string) $request->input('search'))
                    : null,
            ],
        ]);
    }

    public function store(StoreJamaahRequest $request): JsonResponse
    {
        $jamaah = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Jamaah berhasil dibuat.',
            'data' => new JamaahResource($jamaah),
        ], 201);
    }

    public function show(Request $request, Jamaah $jamaah): JsonResponse
    {
        $this->authorizeView($jamaah, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Detail jamaah berhasil diambil.',
            'data' => new JamaahResource($jamaah),
        ]);
    }

    public function update(StoreJamaahRequest $request, Jamaah $jamaah): JsonResponse
    {
        $this->authorizeView($jamaah, $request->user());

        $jamaah = $this->service->update($jamaah, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Jamaah berhasil diperbarui.',
            'data' => new JamaahResource($jamaah),
        ]);
    }

    public function destroy(Request $request, Jamaah $jamaah): JsonResponse
    {
        $this->authorizeView($jamaah, $request->user());

        $this->service->delete($jamaah);

        return response()->json([
            'success' => true,
            'message' => 'Jamaah berhasil dihapus.',
        ]);
    }

    public function approve(Request $request, Jamaah $jamaah): JsonResponse
    {
        $this->authorizeManageApproval($request->user(), $jamaah);

        $jamaah = $this->service->approve($jamaah);

        return response()->json([
            'success' => true,
            'message' => 'Jamaah berhasil disetujui.',
            'data' => new JamaahResource($jamaah),
        ]);
    }

    public function reject(Request $request, Jamaah $jamaah): JsonResponse
    {
        $this->authorizeManageApproval($request->user(), $jamaah);

        $jamaah = $this->service->reject($jamaah);

        return response()->json([
            'success' => true,
            'message' => 'Jamaah berhasil ditolak.',
            'data' => new JamaahResource($jamaah),
        ]);
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
            $query->where('agent_id', $user->id);
            return;
        }

        if ($user->hasRole(['JAMAAH', 'CUSTOMER'])) {
            $query->where('user_id', $user->id);
            return;
        }

        $query->whereRaw('1 = 0');
    }

    protected function applyFilters(Builder $query, Request $request): void
    {
        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->string('approval_status')->toString());
        }

        if ($request->filled('source')) {
            $query->where('source', $request->string('source')->toString());
        }

        if ($request->has('is_active')) {
            $isActive = filter_var(
                $request->input('is_active'),
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            );

            if (!is_null($isActive)) {
                $query->where('is_active', $isActive);
            }
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('jamaah_code', 'like', "%{$search}%")
                    ->orWhere('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('passport_number', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
    }

    protected function authorizeView(Jamaah $jamaah, $user): void
    {
        $allowed = false;

        if ($user->hasRole(['SUPERADMIN', 'ADMIN_PUSAT'])) {
            $allowed = true;
        } elseif ($user->hasRole('ADMIN_CABANG')) {
            $allowed = (int) $jamaah->branch_id === (int) $user->branch_id;
        } elseif ($user->hasRole('AGENT')) {
            $allowed = (int) $jamaah->agent_id === (int) $user->id;
        } elseif ($user->hasRole(['JAMAAH', 'CUSTOMER'])) {
            $allowed = (int) $jamaah->user_id === (int) $user->id;
        }

        abort_unless($allowed, 403, 'Anda tidak memiliki akses ke jamaah ini.');
    }

    protected function authorizeManageApproval($user, Jamaah $jamaah): void
    {
        $allowed = false;

        if ($user->hasRole(['SUPERADMIN', 'ADMIN_PUSAT'])) {
            $allowed = true;
        } elseif ($user->hasRole('ADMIN_CABANG')) {
            $allowed = (int) $jamaah->branch_id === (int) $user->branch_id;
        }

        abort_unless($allowed, 403, 'Anda tidak memiliki izin untuk approval jamaah.');
    }
}