<?php

namespace App\Services\Bookings;

use App\Models\Booking;
use App\Models\PaketDeparture;
use App\Models\PaketDeparturePrice;
use App\Models\Jamaah;
use App\Models\BookingLock;

use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        protected BookingWorkflowService $workflowService,
        protected \App\Services\CodeGeneratorService $codeService,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | CREATE BOOKING (FINAL OTA - CLEAN)
    |--------------------------------------------------------------------------
    */
    public function create(array $data, $user): Booking
    {
        if (!$user) {
            throw new \Exception('User tidak terautentikasi.');
        }

        return DB::transaction(function () use ($data, $user) {

            /*
            |--------------------------------------------------------------------------
            | 1. LOCK DEPARTURE (ANTI RACE)
            |--------------------------------------------------------------------------
            */
            $departure = PaketDeparture::lockForUpdate()
                ->with('paket')
                ->findOrFail($data['paket_departure_id']);

            if ($departure->is_closed) {
                throw new \Exception('Departure sudah ditutup.');
            }

            /*
            |--------------------------------------------------------------------------
            | 2. ANTI DOUBLE BOOKING
            |--------------------------------------------------------------------------
            */
            $existing = Booking::query()
                ->where('user_id', $user->id)
                ->where('paket_departure_id', $departure->id)
                ->whereIn('status', ['waiting_payment', 'partial_paid'])
                ->latest()
                ->first();

            if ($existing) {
                return $existing->load(['paket','departure','jamaahs']);
            }

            /*
            |--------------------------------------------------------------------------
            | 3. VALIDASI LOCK
            |--------------------------------------------------------------------------
            */
            $userLock = BookingLock::query()
                ->where('paket_departure_id', $departure->id)
                ->where('user_id', $user->id)
                ->where('expired_at', '>', now())
                ->lockForUpdate()
                ->first();

            if (!$userLock) {
                throw new \Exception('Lock seat tidak ditemukan / expired');
            }

            /*
            |--------------------------------------------------------------------------
            | 4. ROOM TYPE
            |--------------------------------------------------------------------------
            */
            $roomType = strtolower(trim($data['room_type'] ?? ''));

            if (!in_array($roomType, ['double','triple','quad'])) {
                throw new \Exception('Tipe kamar tidak valid.');
            }

            /*
            |--------------------------------------------------------------------------
            | 5. JAMAAH
            |--------------------------------------------------------------------------
            */
            if ($user->hasRole('JAMAAH')) {
                $jamaahIds = $user->websiteJamaahs()->pluck('id')->toArray();
            } else {
                $jamaahIds = $data['jamaah_ids'] ?? [];
            }

            if (empty($jamaahIds)) {
                throw new \Exception('Minimal 1 jamaah.');
            }

            $seatCount = count($jamaahIds);

            if ($seatCount > $userLock->qty) {
                throw new \Exception('Melebihi seat yang dikunci.');
            }

            /*
            |--------------------------------------------------------------------------
            | 6. QUOTA CHECK
            |--------------------------------------------------------------------------
            */
            $locked = BookingLock::query()
                ->where('paket_departure_id', $departure->id)
                ->where('expired_at', '>', now())
                ->where('user_id', '!=', $user->id)
                ->sum('qty');

            $available = $departure->quota - $departure->booked - $locked;

            if ($available < $seatCount) {
                throw new \Exception('Seat tidak mencukupi.');
            }

            if ($departure->booked + $seatCount > $departure->quota) {
                throw new \Exception('Seat sudah penuh.');
            }

            /*
            |--------------------------------------------------------------------------
            | 7. VALIDATE JAMAAH EXIST
            |--------------------------------------------------------------------------
            */
            $validJamaahIds = Jamaah::whereIn('id', $jamaahIds)
                ->pluck('id')
                ->toArray();

            if (count($validJamaahIds) !== $seatCount) {
                throw new \Exception('Jamaah tidak valid.');
            }

            /*
            |--------------------------------------------------------------------------
            | 8. GET PRICE + CALCULATE PROMO (FINAL OTA ENGINE)
            |--------------------------------------------------------------------------
            */
            $priceRow = PaketDeparturePrice::query()
                ->where('paket_departure_id', $departure->id)
                ->where('room_type', $roomType)
                ->lockForUpdate()
                ->first();

            if (!$priceRow) {
                throw new \Exception('Harga tidak ditemukan.');
            }

            /*
            |--------------------------------------------------------------------------
            | BASE PRICE
            |--------------------------------------------------------------------------
            */
            $basePrice = (float) $priceRow->price;

            $discount   = 0;
            $finalPrice = $basePrice;

            /*
            |--------------------------------------------------------------------------
            | 🔥 PROMO ENGINE (ONLY IF VALID)
            |--------------------------------------------------------------------------
            */
            if (
                $priceRow->promo_type &&
                $priceRow->promo_value &&
                (
                    !$priceRow->promo_expires_at ||
                    now()->lte($priceRow->promo_expires_at)
                )
            ) {

                switch ($priceRow->promo_type) {

                    case 'percent':
                        $discount = ($basePrice * (float) $priceRow->promo_value) / 100;
                        break;

                    case 'fixed':
                        $discount = (float) $priceRow->promo_value;
                        break;
                }

                // safety (no negative price)
                $finalPrice = max(0, $basePrice - $discount);
            }

            /*
            |--------------------------------------------------------------------------
            | TOTAL
            |--------------------------------------------------------------------------
            */
            $totalAmount = $finalPrice * $seatCount;

            /*
            |--------------------------------------------------------------------------
            | 9. GENERATE CODE
            |--------------------------------------------------------------------------
            */
            $bookingCode = $this->codeService->generate('BOOK', 'booking');
            $invoiceNumber = $this->codeService->generate('INV', 'invoice');

            /*
            |--------------------------------------------------------------------------
            | 10. CREATE BOOKING
            |--------------------------------------------------------------------------
            */
            $booking = Booking::create([
                'booking_code' => $bookingCode,
                'invoice_number' => $invoiceNumber,

                'paket_id' => $departure->paket_id,
                'paket_departure_id' => $departure->id,

                'user_id' => $user->id,
                'created_by' => $user->id,

                'room_type' => $roomType,
                'qty' => $seatCount,

                // 🔥 SNAPSHOT (FINAL)
                'price_per_person_snapshot' => $finalPrice,
                'original_price_snapshot'   => $basePrice,
                'discount_snapshot'         => $discount,
                'promo_label_snapshot'      => $priceRow->promo_label,

                'total_amount' => $totalAmount,

                'status' => 'waiting_payment',
                'expired_at' => now()->addHours(config('booking.expired_hours', 24)),
            ]);

            /*
            |--------------------------------------------------------------------------
            | 11. ATTACH JAMAAH (🔥 FIX PIVOT PRICE)
            |--------------------------------------------------------------------------
            */
            $attachData = [];

            foreach ($validJamaahIds as $jid) {
                $attachData[$jid] = [
                    'room_type' => $roomType,
                    'price'     => $finalPrice,
                ];
            }

            $booking->jamaahs()->attach($attachData);

            /*
            |--------------------------------------------------------------------------
            | 12. CONSUME LOCK
            |--------------------------------------------------------------------------
            */
            $userLock->delete();

            /*
            |--------------------------------------------------------------------------
            | 13. UPDATE QUOTA
            |--------------------------------------------------------------------------
            */
            $departure->increment('booked', $seatCount);

            if ($departure->booked >= $departure->quota) {
                $departure->update(['is_closed' => true]);
            }

            /*
            |--------------------------------------------------------------------------
            | DONE
            |--------------------------------------------------------------------------
            */
            return $booking->load([
                'paket',
                'departure',
                'jamaahs'
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | WORKFLOW
    |--------------------------------------------------------------------------
    */
    public function confirm(Booking $booking): Booking
    {
        return $this->workflowService->confirm($booking);
    }

    public function cancel(Booking $booking): Booking
    {
        return $this->workflowService->cancel($booking);
    }
}