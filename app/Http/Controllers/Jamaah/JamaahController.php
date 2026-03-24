<?php

namespace App\Http\Controllers\Jamaah;

use App\Http\Controllers\Controller;
use App\Models\Jamaah;
use App\Models\Branch;
use App\Models\Booking;
use App\Models\User;
use App\Services\Jamaah\JamaahService;
use Illuminate\Http\Request;

class JamaahController extends Controller
{
    protected JamaahService $service;

    public function __construct(JamaahService $service)
    {
        $this->service = $service;
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $this->authorize('viewAny', Jamaah::class);

        $query = Jamaah::with(['branch','agent'])
            ->latest();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $jamaahs = $query->paginate(15)
            ->withQueryString();

        return view('jamaahs.index', compact('jamaahs'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $this->authorize('create', Jamaah::class);

        $user = auth()->user();

        $branches = $user->isSuperAdmin() || $user->isAdminPusat()
            ? Branch::active()->get()
            : Branch::where('id',$user->branch_id)->get();

        return view('jamaahs.create', compact('branches'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $this->authorize('create', Jamaah::class);

        $validated = $this->validateData($request);

        $this->service->create($validated);

        return redirect()
            ->route('jamaah.index')
            ->with('success','Jamaah berhasil dibuat.');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(Jamaah $jamaah)
    {
        $this->authorize('view', $jamaah);

        $jamaah->load([
            'branch',
            'agent',
            'documents',
            'bookings.paket',
            'bookings.departure'
        ]);

        return view('jamaahs.show', compact('jamaah'));
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(Jamaah $jamaah)
    {
        $this->authorize('update', $jamaah);

        $user = auth()->user();

        if ($user->isSuperAdmin() || $user->isAdminPusat()) {

            $branches = Branch::active()->get();

            $agents = User::role('AGENT')->get();
        }
        else {

            $branches = Branch::where('id',$user->branch_id)->get();

            $agents = User::role('AGENT')
                ->where('branch_id',$user->branch_id)
                ->get();
        }

        return view('jamaahs.edit', compact(
            'jamaah',
            'branches',
            'agents'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Jamaah $jamaah)
    {
        $this->authorize('update', $jamaah);

        $validated = $this->validateData($request);

        $this->service->update($jamaah,$validated);

        return redirect()
            ->route('jamaah.index')
            ->with('success','Jamaah berhasil diupdate.');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */

    public function destroy(Jamaah $jamaah)
    {
        $this->authorize('delete', $jamaah);

        $this->service->delete($jamaah);

        return redirect()
            ->route('jamaah.index')
            ->with('success','Jamaah berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVAL
    |--------------------------------------------------------------------------
    */

    public function approve(Jamaah $jamaah)
    {
        $this->authorize('update', $jamaah);

        $this->service->approve($jamaah);

        return back()->with('success','Jamaah disetujui.');
    }

    public function reject(Jamaah $jamaah)
    {
        $this->authorize('update', $jamaah);

        $this->service->reject($jamaah);

        return back()->with('success','Jamaah ditolak.');
    }

    /*
    |--------------------------------------------------------------------------
    | BOOKING HISTORY
    |--------------------------------------------------------------------------
    */

    public function bookingHistory(Request $request)
    {
        $this->authorize('viewAny', Booking::class);

        $query = Booking::with([
            'jamaahs',
            'paket',
            'departure',
            'agent',
            'branch'
        ])->latest();

        if ($request->filled('search')) {

            $query->whereHas('jamaahs', function ($q) use ($request) {

                $q->where('nama_lengkap','like','%'.$request->search.'%')
                  ->orWhere('jamaah_code','like','%'.$request->search.'%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status',$request->status);
        }

        $bookings = $query->paginate(15)
            ->withQueryString();

        return view('jamaahs.booking-history', compact('bookings'));
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    private function validateData(Request $request): array
    {
        return $request->validate([

            'branch_id' => 'nullable|exists:branches,id',

            'agent_id'  => 'nullable|exists:users,id',

            'source' => 'required|in:offline,branch,agent,website',

            'nama_lengkap' => 'required|string|max:255',

            'gender' => 'nullable|in:L,P',

            'tanggal_lahir' => 'nullable|date',

            'tempat_lahir' => 'nullable|string|max:255',

            'nik' => 'nullable|string|max:255',

            'passport_number' => 'nullable|string|max:255',

            'phone' => 'nullable|string|max:255',

            'email' => 'nullable|email|max:255',

            'address' => 'nullable|string',

            'city' => 'nullable|string|max:255',

            'province' => 'nullable|string|max:255',

            'is_active' => 'boolean',
        ]);
    }
}