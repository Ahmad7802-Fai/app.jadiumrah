<?php

namespace App\Services\Pakets;

use App\Models\Paket;
use App\Models\PaketHotel;
use App\Models\PaketDestination;
use App\Models\PaketDeparture;
use App\Models\PaketDeparturePrice;
use App\Models\Destination;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use App\Services\Media\MediaService;

class PaketService
{
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create(array $data): Paket
    {
        return DB::transaction(function () use ($data) {

            $paket = Paket::create(
                $this->mapMainData($data)
            );

            $this->handleMediaCreate($paket, $data);

            $this->syncHotels($paket, $data['hotels'] ?? []);
            $this->syncItinerary($paket, $data['itinerary'] ?? []);
            $this->syncDepartures($paket, $data['departures'] ?? []);

            return $paket->load([
                'hotels',
                'destinations.destination',
                'departures.prices'
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Paket $paket, array $data): Paket
    {
        return DB::transaction(function () use ($paket, $data) {

            $paket->update(
                $this->mapMainData($data, $paket)
            );

            $this->handleMediaUpdate($paket, $data);

            if (isset($data['hotels'])) {
                $this->syncHotels($paket, $data['hotels']);
            }

            if (isset($data['itinerary'])) {
                $this->syncItinerary($paket, $data['itinerary']);
            }

            if (isset($data['departures'])) {
                $this->syncDepartures($paket, $data['departures']);
            }

            return $paket->load([
                'hotels',
                'destinations.destination',
                'departures.prices'
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE (SAFE)
    |--------------------------------------------------------------------------
    */

    public function delete(Paket $paket): void
    {
        DB::transaction(function () use ($paket) {

            // delete media
            if ($paket->thumbnail) {
                Storage::disk('public')->delete($paket->thumbnail);
            }

            if ($paket->gallery) {
                foreach ($paket->gallery as $img) {
                    Storage::disk('public')->delete($img);
                }
            }

            // delete relations (avoid orphan)
            $paket->hotels()->delete();
            $paket->destinations()->delete();
            $paket->departures()->each(function ($dep) {
                $dep->prices()->delete();
                $dep->delete();
            });

            $paket->delete();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | MAIN DATA (🔥 PROMO INCLUDED)
    |--------------------------------------------------------------------------
    */

    private function mapMainData(array $data, ?Paket $paket = null): array
    {
        return [

            'name' => $data['name'],
            'code' => $data['code'],

            'slug' => $paket?->slug
                ?? Str::slug($data['name']) . '-' . Str::random(5),

            'departure_city' => $data['departure_city'] ?? null,
            'duration_days' => $data['duration_days'] ?? null,
            'airline' => $data['airline'] ?? null,

            'short_description' => $data['short_description'] ?? null,
            'description' => $data['description'] ?? null,

            /*
            |--------------------------------------------------------------------------
            | PROMO
            |--------------------------------------------------------------------------
            */
            'promo_label' => $data['promo_label'] ?? null,
            'promo_value' => $data['promo_value'] ?? null,
            'promo_type' => $data['promo_type'] ?? null,
            'promo_expires_at' => $data['promo_expires_at'] ?? null,

            'is_active' => $data['is_active'] ?? true,
            'is_published' => $data['is_published'] ?? false,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MEDIA CREATE
    |--------------------------------------------------------------------------
    */

    private function handleMediaCreate(Paket $paket, array $data): void
    {
        /*
        |--------------------------------------------------------------------------
        | THUMBNAIL (BASE64 PRIORITY)
        |--------------------------------------------------------------------------
        */
        if (!empty($data['thumbnail_base64'])) {

            $img = $this->mediaService->uploadBase64Image($data['thumbnail_base64'], 'pakets');

            $paket->update([
                'thumbnail' => $img['full'],
                'thumbnail_small' => $img['thumb'],
            ]);
        }
        elseif (!empty($data['thumbnail'])) {

            $img = $this->mediaService->uploadImage($data['thumbnail']);

            $paket->update([
                'thumbnail' => $img['full'],
                'thumbnail_small' => $img['thumb'],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | GALLERY
        |--------------------------------------------------------------------------
        */
        $gallery = [];

        // BASE64 (🔥 dari cropper)
        if (!empty($data['gallery_base64'])) {
            foreach ($data['gallery_base64'] as $img) {
                $gallery[] = $this->mediaService->uploadBase64Single($img, 'pakets');
            }
        }

        // FILE (fallback)
        if (!empty($data['gallery'])) {
            foreach ($data['gallery'] as $file) {
                $gallery[] = $this->mediaService->uploadSingle($file);
            }
        }

        if (!empty($gallery)) {
            $paket->update(['gallery' => $gallery]);
        }
    }

    // private function handleMediaCreate(Paket $paket, array $data): void
    // {
    //     if (!empty($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {

    //         $path = $data['thumbnail']->store('pakets', 'public');

    //         $paket->update(['thumbnail' => $path]);
    //     }

    //     if (!empty($data['gallery'])) {

    //         $gallery = [];

    //         foreach ($data['gallery'] as $file) {

    //             if ($file instanceof UploadedFile) {
    //                 $gallery[] = $file->store('pakets', 'public');
    //             }
    //         }

    //         $paket->update(['gallery' => $gallery]);
    //     }
    // }

    /*
    |--------------------------------------------------------------------------
    | MEDIA UPDATE
    |--------------------------------------------------------------------------
    */

    private function handleMediaUpdate(Paket $paket, array $data): void
    {
        /*
        |--------------------------------------------------------------------------
        | THUMBNAIL
        |--------------------------------------------------------------------------
        */
        if (!empty($data['thumbnail_base64']) || !empty($data['thumbnail'])) {

            $this->mediaService->delete($paket->thumbnail);
            $this->mediaService->delete($paket->thumbnail_small);

            if (!empty($data['thumbnail_base64'])) {
                $img = $this->mediaService->uploadBase64Image($data['thumbnail_base64'], 'pakets');
            } else {
                $img = $this->mediaService->uploadImage($data['thumbnail']);
            }

            $paket->update([
                'thumbnail' => $img['full'],
                'thumbnail_small' => $img['thumb'],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | GALLERY
        |--------------------------------------------------------------------------
        */
        $gallery = $paket->gallery ?? [];

        // REMOVE
        if (!empty($data['remove_gallery'])) {

            $this->mediaService->deleteMany($data['remove_gallery']);

            $gallery = array_filter(
                $gallery,
                fn($g) => !in_array($g, $data['remove_gallery'])
            );
        }

        // ADD BASE64
        if (!empty($data['gallery_base64'])) {
            foreach ($data['gallery_base64'] as $img) {
                $gallery[] = $this->mediaService->uploadBase64Single($img, 'pakets');
            }
        }

        // ADD FILE
        if (!empty($data['gallery'])) {
            foreach ($data['gallery'] as $file) {
                $gallery[] = $this->mediaService->uploadSingle($file);
            }
        }

        $paket->update([
            'gallery' => array_values($gallery)
        ]);
    }

    // private function handleMediaUpdate(Paket $paket, array $data): void
    // {
    //     if (!empty($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {

    //         if ($paket->thumbnail) {
    //             Storage::disk('public')->delete($paket->thumbnail);
    //         }

    //         $path = $data['thumbnail']->store('pakets', 'public');

    //         $paket->update(['thumbnail' => $path]);
    //     }

    //     $gallery = $paket->gallery ?? [];

    //     if (!empty($data['remove_gallery'])) {

    //         foreach ($data['remove_gallery'] as $img) {

    //             Storage::disk('public')->delete($img);

    //             $gallery = array_filter(
    //                 $gallery,
    //                 fn($g) => $g !== $img
    //             );
    //         }
    //     }

    //     if (!empty($data['gallery'])) {

    //         foreach ($data['gallery'] as $file) {

    //             if ($file instanceof UploadedFile) {
    //                 $gallery[] = $file->store('pakets', 'public');
    //             }
    //         }
    //     }

    //     $paket->update([
    //         'gallery' => array_values($gallery)
    //     ]);
    // }

    /*
    |--------------------------------------------------------------------------
    | HOTELS
    |--------------------------------------------------------------------------
    */

    private function syncHotels(Paket $paket, array $hotels): void
    {
        $paket->hotels()->delete();

        foreach ($hotels as $hotel) {

            if (!empty($hotel['hotel_name'])) {

                PaketHotel::create([
                    'paket_id' => $paket->id,
                    'city' => $hotel['city'] ?? null,
                    'hotel_name' => $hotel['hotel_name'],
                    'rating' => $hotel['rating'] ?? null,
                    'distance_to_haram' => $hotel['distance_to_haram'] ?? null,
                ]);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ITINERARY
    |--------------------------------------------------------------------------
    */

    private function syncItinerary(Paket $paket, array $items): void
    {
        // reset
        $paket->destinations()->delete();

        foreach ($items as $index => $item) {

            $destinationId = $item['destination_id'] ?? null;
            $destinationName = trim($item['destination_name'] ?? '');

            /* =====================================================
            🔥 FIX UTAMA: HANDLE "__new__"
            ===================================================== */
            if ($destinationId === '__new__') {
                $destinationId = null;
            }

            /* =====================================================
            🔥 CREATE DESTINATION BARU
            ===================================================== */
            if (!$destinationId && $destinationName !== '') {

                $destination = Destination::firstOrCreate(
                    ['city' => $destinationName],
                    [
                        'country' => 'Saudi Arabia',
                        'type' => 'tour'
                    ]
                );

                $destinationId = $destination->id;
            }

            /* =====================================================
            ⛔ SKIP JIKA MASIH TIDAK ADA
            ===================================================== */
            if (!$destinationId) continue;

            /* =====================================================
            ✅ INSERT
            ===================================================== */
            PaketDestination::create([
                'paket_id'       => $paket->id,
                'destination_id' => $destinationId,
                'day_order'      => $item['day_order'] ?? ($index + 1),
                'note'           => $item['note'] ?? null,
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DEPARTURES
    |--------------------------------------------------------------------------
    */

    private function syncDepartures(Paket $paket, array $departures): void
    {
        $paket->departures()->delete();

        foreach ($departures as $dep) {

            if (empty($dep['departure_date']) || empty($dep['quota'])) {
                continue;
            }

            $departure = PaketDeparture::create([
                'paket_id' => $paket->id,
                'departure_date' => $dep['departure_date'],
                'return_date' => $dep['return_date'] ?? null,
                'quota' => $dep['quota'],
                'booked' => 0,
                'is_active' => true,
                'is_closed' => false,
            ]);

            foreach ($dep['prices'] ?? [] as $price) {

                if (empty($price['room_type']) || empty($price['price'])) {
                    continue;
                }

                PaketDeparturePrice::create([
                    'paket_departure_id' => $departure->id,
                    'room_type' => $price['room_type'],
                    'price' => $price['price'],

                    // 🔥 PROMO (INI YANG KEMARIN HILANG)
                    'promo_type' => $price['promo_type'] ?? null,
                    'promo_value' => $price['promo_value'] ?? null,
                    'promo_label' => $price['promo_label'] ?? null,
                    'promo_expires_at' => $price['promo_expires_at'] ?? null,
                ]);
            }
        }
    }

}