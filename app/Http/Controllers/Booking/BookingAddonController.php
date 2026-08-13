<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingAddon;
use App\Models\MarketingAddon;
use App\Services\Marketing\AddonService;
use Illuminate\Http\Request;

class BookingAddonController extends Controller
{
    public function __construct(
        protected AddonService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | ATTACH ADDON
    |--------------------------------------------------------------------------
    */
    public function store(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'marketing_addon_id' => 'required|exists:marketing_addons,id',
            'qty' => 'required|integer|min:1'
        ]);

        $addon = MarketingAddon::findOrFail($validated['marketing_addon_id']);

        $this->service->attachToBooking(
            $booking,
            $addon,
            $validated['qty']
        );

        return back()->with('success','Add-On berhasil ditambahkan.');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE QTY
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Booking $booking, BookingAddon $bookingAddon)
    {
        $validated = $request->validate([
            'qty' => 'required|integer|min:0'
        ]);

        $this->service->updateQty($bookingAddon, $validated['qty']);

        return back()->with('success','Qty berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE
    |--------------------------------------------------------------------------
    */
    public function destroy(Booking $booking, BookingAddon $bookingAddon)
    {
        $this->service->removeFromBooking($bookingAddon);

        return back()->with('success','Add-On dihapus.');
    }
}