<?php

namespace App\Http\Controllers\Api\V1\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\PaketDeparture;
use App\Models\BookingLock;

class BookingLockController extends Controller
{
    public function __construct()
    {
        // 🔥 hanya method ini yang butuh login
        $this->middleware('auth:sanctum')->only([
            'lock',
            'extend',
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | LOCK SEAT
    |--------------------------------------------------------------------------
    */
    public function lock(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'paket_departure_id' => 'required|exists:paket_departures,id',
            'qty' => 'required|integer|min:1|max:10',
        ]);

        $departure = PaketDeparture::findOrFail($request->paket_departure_id);

        // 🔥 hitung real availability
        $locked = BookingLock::where('paket_departure_id', $departure->id)
            ->where('expired_at', '>', now())
            ->sum('qty');

        $available = $departure->quota - $departure->booked - $locked;

        if ($available < $request->qty) {
            return response()->json([
                'success' => false,
                'message' => 'Seat tidak mencukupi',
            ], 422);
        }

        // 🔥 delete old lock user
        BookingLock::where('user_id', $user->id)->delete();

        $lock = BookingLock::create([
            'paket_departure_id' => $departure->id,
            'user_id' => $user->id,
            'qty' => $request->qty,
            'expired_at' => now()->addMinutes(10),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Seat berhasil dikunci',
            'data' => $lock
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | EXTEND LOCK
    |--------------------------------------------------------------------------
    */
    public function extend(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'paket_departure_id' => 'required|exists:paket_departures,id',
        ]);

        $lock = BookingLock::where('user_id', $user->id)
            ->where('paket_departure_id', $request->paket_departure_id)
            ->where('expired_at', '>', now())
            ->first();

        if (!$lock) {
            return response()->json([
                'success' => false,
                'message' => 'Lock tidak ditemukan',
            ], 404);
        }

        $lock->update([
            'expired_at' => now()->addMinutes(10),
        ]);

        return response()->json([
            'success' => true,
            'expired_at' => $lock->expired_at,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | AVAILABILITY
    |--------------------------------------------------------------------------
    */
    public function availability(Request $request): JsonResponse
    {
        $request->validate([
            'paket_departure_id' => 'required|exists:paket_departures,id',
        ]);

        $departure = PaketDeparture::findOrFail($request->paket_departure_id);

        $locked = BookingLock::where('paket_departure_id', $departure->id)
            ->where('expired_at', '>', now())
            ->sum('qty');

        $available = $departure->quota - $departure->booked - $locked;

        return response()->json([
            'success' => true,
            'data' => [
                'quota' => $departure->quota,
                'booked' => $departure->booked,
                'locked' => $locked,
                'available' => max(0, $available),
            ]
        ]);
    }
}