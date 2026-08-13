<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Jamaah;
use App\Models\JamaahDocument;   // 🔥 WAJIB
use App\Services\Jamaah\JamaahService;
use Illuminate\Http\Request;

class JamaahController extends Controller
{
    public function __construct(
        protected JamaahService $jamaahService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $this->authorize('viewAny', Jamaah::class);

        $user = $request->user();

        $query = Jamaah::query()
            ->with(['branch','agent'])
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
            $query->where('branch_id',$user->branch_id);
        }

        elseif ($user->hasRole('AGENT')) {
            $query->where('agent_id',$user->id);
        }

        else {
            $query->where('user_id',$user->id);
        }

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function($q) use ($search){

                $q->where('nama_lengkap','like',"%{$search}%")
                  ->orWhere('passport_number','like',"%{$search}%")
                  ->orWhere('nik','like',"%{$search}%")
                  ->orWhere('phone','like',"%{$search}%")
                  ->orWhere('jamaah_code','like',"%{$search}%");

            });

        }

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('agent_id')) {
            $query->where('agent_id',$request->agent_id);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id',$request->branch_id);
        }

        if ($request->filled('approval_status')) {
            $query->where('approval_status',$request->approval_status);
        }

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $perPage = $request->get('per_page',15);

        $jamaahs = $query->paginate($perPage);

        return response()->json($jamaahs);
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $this->authorize('create', Jamaah::class);

        $validated = $request->validate([

            'nama_lengkap'   => 'required|string|max:255',

            'nik'            => 'required|string|max:255|unique:jamaahs,nik',

            'gender'         => 'nullable|string',

            'tanggal_lahir'  => 'nullable|date',

            'tempat_lahir'   => 'nullable|string',

            'passport_number'=> 'nullable|string',

            'seat_number'    => 'nullable|string|max:10',

            'phone'          => 'nullable|string',

            'email'          => 'nullable|email',

            'address'        => 'nullable|string',

            'city'           => 'nullable|string',

            'province'       => 'nullable|string',

            'is_active'      => 'nullable|boolean',

        ]);

        $jamaah = $this->jamaahService->create($validated);

        return response()->json([
            'message' => 'Jamaah berhasil dibuat',
            'data'    => $jamaah
        ],201);
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(Jamaah $jamaah)
    {
        $this->authorize('view',$jamaah);

        $jamaah->load([
            'branch',
            'agent',
            'documents',
            'bookings',
            'bookings.paket',
            'bookings.departure',
            'bookings.payments'
        ]);

        // tambahkan url dokumen
        $jamaah->documents->transform(function ($doc) {
            $doc->url = asset('storage/'.$doc->file_path);
            return $doc;
        });

        return response()->json([
            'data' => $jamaah
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Jamaah $jamaah)
    {
        $this->authorize('update',$jamaah);

        $validated = $request->validate([

            'nama_lengkap'   => 'sometimes|required|string|max:255',

            'nik'            => 'sometimes|required|string|max:255|unique:jamaahs,nik,' . $jamaah->id,

            'gender'         => 'nullable|string',

            'tanggal_lahir'  => 'nullable|date',

            'tempat_lahir'   => 'nullable|string',

            'passport_number'=> 'nullable|string',

            'seat_number'    => 'nullable|string|max:10',

            'phone'          => 'nullable|string',

            'email'          => 'nullable|email',

            'address'        => 'nullable|string',

            'city'           => 'nullable|string',

            'province'       => 'nullable|string',

            'is_active'      => 'nullable|boolean',

            'approval_status'=> 'nullable|in:pending,approved,rejected',

        ]);

        $jamaah = $this->jamaahService->update($jamaah,$validated);

        return response()->json([
            'message' => 'Jamaah berhasil diperbarui',
            'data'    => $jamaah
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(Jamaah $jamaah)
    {
        $this->authorize('delete',$jamaah);

        $this->jamaahService->delete($jamaah);

        return response()->json([
            'message' => 'Jamaah berhasil dihapus'
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | MY JAMAAH
    |--------------------------------------------------------------------------
    */

    public function me(Request $request)
    {
        $user = $request->user();

        $query = Jamaah::query()
            ->where('is_active',true)
            ->where('approval_status','approved')
            ->latest();

        if ($user->hasRole(['SUPERADMIN','ADMIN_PUSAT'])) {
            // full access
        }

        elseif ($user->hasRole('ADMIN_CABANG')) {
            $query->where('branch_id',$user->branch_id);
        }

        elseif ($user->hasRole('AGENT')) {
            $query->where('agent_id',$user->id);
        }

        else {
            $query->where('user_id',$user->id);
        }

        return response()->json([
            'data' => $query->get()
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPLOAD DOCUMENT
    |--------------------------------------------------------------------------
    */

    public function uploadDocument(Request $request, Jamaah $jamaah)
    {

        $this->authorize('update', $jamaah);

        $this->authorize('create', JamaahDocument::class);

        $validated = $request->validate([
            'document_type' => 'required|in:passport,visa,ktp,kk,vaccine,other',
            'file' => 'required|file|max:4096',
            'expired_at' => 'nullable|date'
        ]);

        $document = $this->jamaahService->uploadDocument(
            $jamaah,
            $validated['document_type'],
            $request->file('file'),
            $validated['expired_at'] ?? null
        );

        return response()->json([
            'message' => 'Dokumen berhasil diupload',
            'data' => $document
        ]);
    }

}