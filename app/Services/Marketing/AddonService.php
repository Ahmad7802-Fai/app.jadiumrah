<?php

namespace App\Services\Marketing;

use App\Models\MarketingAddon;
use App\Models\Booking;
use App\Models\BookingAddon;
use Illuminate\Support\Facades\DB;

class AddonService
{
    /*
    |--------------------------------------------------------------------------
    | CREATE MASTER ADDON
    |--------------------------------------------------------------------------
    */
    public function create(array $data): MarketingAddon
    {
        return DB::transaction(function () use ($data) {

            return MarketingAddon::create([
                'name'          => $data['name'],
                'code'          => strtoupper($data['code']),
                'description'   => $data['description'] ?? null,
                'selling_price' => $data['selling_price'],
                'cost_price'    => $data['cost_price'] ?? 0,
                'is_active'     => $data['is_active'] ?? true,
                'created_by'    => auth()->id(),
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE MASTER ADDON
    |--------------------------------------------------------------------------
    */
    public function update(MarketingAddon $addon, array $data): MarketingAddon
    {
        return DB::transaction(function () use ($addon, $data) {

            $addon->update([
                'name'          => $data['name'],
                'description'   => $data['description'] ?? null,
                'selling_price' => $data['selling_price'],
                'cost_price'    => $data['cost_price'] ?? 0,
                'is_active'     => $data['is_active'] ?? true,
            ]);

            return $addon;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE MASTER ADDON
    |--------------------------------------------------------------------------
    */
    public function delete(MarketingAddon $addon): void
    {
        DB::transaction(function () use ($addon) {
            $addon->delete();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ATTACH ADDON TO BOOKING
    |--------------------------------------------------------------------------
    */
    public function attachToBooking(
        Booking $booking,
        MarketingAddon $addon,
        int $qty = 1
    ): BookingAddon {

        return DB::transaction(function () use ($booking, $addon, $qty) {

            $existing = BookingAddon::where('booking_id', $booking->id)
                ->where('marketing_addon_id', $addon->id)
                ->first();

            if ($existing) {
                return $this->updateQty($existing, $existing->qty + $qty);
            }

            $total = $addon->selling_price * $qty;

            $bookingAddon = BookingAddon::create([
                'booking_id'         => $booking->id,
                'marketing_addon_id' => $addon->id,
                'qty'                => $qty,
                'price'              => $addon->selling_price,
                'cost_price'         => $addon->cost_price,
                'total'              => $total,
            ]);

            $this->syncBookingTotal($booking);

            return $bookingAddon;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE QTY
    |--------------------------------------------------------------------------
    */
    public function updateQty(BookingAddon $bookingAddon, int $qty): BookingAddon
    {
        return DB::transaction(function () use ($bookingAddon, $qty) {

            if ($qty <= 0) {
                return $this->removeFromBooking($bookingAddon);
            }

            $bookingAddon->update([
                'qty'   => $qty,
                'total' => $bookingAddon->price * $qty,
            ]);

            $this->syncBookingTotal($bookingAddon->booking);

            return $bookingAddon;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE ADDON FROM BOOKING
    |--------------------------------------------------------------------------
    */
    public function removeFromBooking(BookingAddon $bookingAddon): void
    {
        DB::transaction(function () use ($bookingAddon) {

            $booking = $bookingAddon->booking;

            $bookingAddon->delete();

            $this->syncBookingTotal($booking);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | SYNC BOOKING TOTAL
    |--------------------------------------------------------------------------
    */
    protected function syncBookingTotal(Booking $booking): void
    {
        $addonTotal = $booking->addons()->sum('total');

        $booking->update([
            'total_amount' => $booking->base_price + $addonTotal
        ]);
    }
}