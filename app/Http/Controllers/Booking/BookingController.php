<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Jamaah;
use App\Models\Paket;
use App\Models\BookingLock;
use App\Services\Bookings\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $service
    ) {
        $this->authorizeResource(Booking::class, 'booking');
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $user = auth()->user();

        $query = Booking::query()
            ->with([
                'jamaahs:id,nama_lengkap',
                'paket:id,name',
                'departure:id,paket_id,departure_date'
            ])
            ->latest();

        if ($user->isAgent()) {
            $query->where('agent_id', $user->id);
        } elseif ($user->isAdminCabang()) {
            $query->where('branch_id', $user->branch_id);
        }

        $bookings = $query
            ->paginate(15)
            ->withQueryString();

        return view('bookings.index', compact('bookings'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $user = auth()->user();

        $jamaahs = Jamaah::approved()
            ->visibleFor($user)
            ->get();

        $pakets = Paket::active()
            ->with(['departures' => function ($q) {
                $q->where('is_active', true)
                  ->where('is_closed', false)
                  ->orderBy('departure_date');
            }])
            ->get();

        return view('bookings.create', compact(
            'jamaahs',
            'pakets'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE (🔥 SYNC WITH SERVICE FINAL)
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jamaah_ids'         => 'required|array|min:1',
            'jamaah_ids.*'       => 'exists:jamaahs,id',
            'paket_departure_id' => 'required|exists:paket_departures,id',
            'room_type'          => 'required|in:double,triple,quad',
        ]);

        try {

            $user = auth()->user();
            $qty  = count($validated['jamaah_ids']);

            /*
            |--------------------------------------------------------------------------
            | 🔥 1. AUTO LOCK (WAJIB)
            |--------------------------------------------------------------------------
            */
            $lock = BookingLock::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'paket_departure_id' => $validated['paket_departure_id'],
                ],
                [
                    'qty' => $qty,
                    'expired_at' => now()->addMinutes(10),
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | 🔥 2. CREATE VIA SERVICE
            |--------------------------------------------------------------------------
            */
            $booking = $this->service->create($validated, $user);

            /*
            |--------------------------------------------------------------------------
            | 🔥 3. HANDLE IDEMPOTENCY (ALREADY EXIST)
            |--------------------------------------------------------------------------
            */
            if (!$booking) {
                return back()->with('error', 'Booking gagal dibuat.');
            }

            /*
            |--------------------------------------------------------------------------
            | SUCCESS
            |--------------------------------------------------------------------------
            */
            return redirect()
                ->route('bookings.show', $booking->id)
                ->with('success', 'Booking berhasil dibuat.');

        } catch (\Throwable $e) {

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */
    public function show(Booking $booking)
    {
        $booking->load([
            'jamaahs',
            'paket',
            'departure',
            'commissionLogs'
        ]);

        return view('bookings.show', compact('booking'));
    }

    /*
    |--------------------------------------------------------------------------
    | CONFIRM
    |--------------------------------------------------------------------------
    */
    public function confirm(Booking $booking)
    {
        $this->authorize('approve', $booking);

        try {

            $this->service->confirm($booking);

            return back()->with(
                'success',
                'Booking berhasil dikonfirmasi.'
            );

        } catch (\Throwable $e) {

            return back()->with(
                'error',
                $e->getMessage()
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL
    |--------------------------------------------------------------------------
    */
    public function cancel(Booking $booking)
    {
        $this->authorize('delete', $booking);

        try {

            $this->service->cancel($booking);

            return back()->with(
                'success',
                'Booking berhasil dibatalkan.'
            );

        } catch (\Throwable $e) {

            return back()->with(
                'error',
                $e->getMessage()
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy(Booking $booking)
    {
        $this->authorize('delete', $booking);

        $booking->delete();

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Booking berhasil dihapus.');
    }
}