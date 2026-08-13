<?php

namespace App\Http\Resources\Api\V1\Paket;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaketDeparturePriceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $basePrice = $this->price !== null ? (float) $this->price : 0;

        /*
        |--------------------------------------------------------------------------
        | 🔥 CALCULATE PROMO (SINGLE SOURCE)
        |--------------------------------------------------------------------------
        */
        $discount = 0;

        if (
            $this->promo_type &&
            $this->promo_value &&
            (
                !$this->promo_expires_at ||
                now()->lte($this->promo_expires_at)
            )
        ) {
            if ($this->promo_type === 'percent') {
                $discount = $basePrice * ($this->promo_value / 100);
            }

            if ($this->promo_type === 'fixed') {
                $discount = (float) $this->promo_value;
            }
        }

        $final = max(0, $basePrice - $discount);

        return [
            'id' => $this->id,

            'room_type' => $this->room_type,

            /*
            |--------------------------------------------------------------------------
            | BASE PRICE
            |--------------------------------------------------------------------------
            */
            'price' => $basePrice,
            'price_label' => $basePrice
                ? 'Rp' . number_format($basePrice, 0, ',', '.')
                : null,

            /*
            |--------------------------------------------------------------------------
            | 🔥 FINAL PRICE (CONSISTENT)
            |--------------------------------------------------------------------------
            */
            'final_price' => $final,
            'final_price_label' => $final
                ? 'Rp' . number_format($final, 0, ',', '.')
                : null,

            /*
            |--------------------------------------------------------------------------
            | DISCOUNT
            |--------------------------------------------------------------------------
            */
            'discount' => $discount,

            /*
            |--------------------------------------------------------------------------
            | PROMO
            |--------------------------------------------------------------------------
            */
            'has_promo' => $discount > 0,
            'promo_label' => $this->promo_label,
            'promo_type' => $this->promo_type,
            'promo_value' => $this->promo_value,

            /*
            |--------------------------------------------------------------------------
            | UX
            |--------------------------------------------------------------------------
            */
            'discount_percent' => $basePrice > 0
                ? round(($discount / $basePrice) * 100)
                : 0,
        ];
    }

}