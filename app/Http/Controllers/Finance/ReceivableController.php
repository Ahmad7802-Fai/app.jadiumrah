<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class ReceivableController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with([
                'jamaahs',
                'branch',
                'paket',
                'payments',
                'refunds'
            ])
            ->where('status', '!=', 'cancelled')
            ->get()
            ->map(function ($booking) {

                // 🔥 Gunakan collection (bukan query ulang)
                $totalPaid = $booking->payments
                    ->where('status', 'paid')
                    ->sum('amount');

                $totalRefund = $booking->refunds
                    ->where('status', 'approved')
                    ->sum('amount');

                $finalPaid = $totalPaid - $totalRefund;

                if ($finalPaid < 0) {
                    $finalPaid = 0;
                }

                $booking->receivable = $booking->total_amount - $finalPaid;

                return $booking;
            })
            ->filter(fn($booking) => $booking->receivable > 0);

        return view('finance.receivables.index', compact('bookings'));
    }
}