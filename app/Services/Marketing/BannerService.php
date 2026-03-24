<?php

namespace App\Services\Marketing;

use App\Models\MarketingBanner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BannerService
{
    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create(array $data): MarketingBanner
    {
        return DB::transaction(function () use ($data) {

            $imagePath = $data['image']->store('banners', 'public');

            $mobileImagePath = null;
            if (!empty($data['mobile_image'])) {
                $mobileImagePath = $data['mobile_image']
                    ->store('banners', 'public');
            }

            return MarketingBanner::create([
                ...$data,
                'image' => $imagePath,
                'mobile_image' => $mobileImagePath,
                'status' => 'draft',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(MarketingBanner $banner, array $data): MarketingBanner
    {
        return DB::transaction(function () use ($banner, $data) {

            $updateData = $data;

            /*
            |--------------------------------------------------------------------------
            | HANDLE IMAGE REUPLOAD
            |--------------------------------------------------------------------------
            */
            if (!empty($data['image'])) {

                if ($banner->image &&
                    Storage::disk('public')->exists($banner->image)) {
                    Storage::disk('public')->delete($banner->image);
                }

                $updateData['image'] =
                    $data['image']->store('banners', 'public');
            }

            if (!empty($data['mobile_image'])) {

                if ($banner->mobile_image &&
                    Storage::disk('public')->exists($banner->mobile_image)) {
                    Storage::disk('public')->delete($banner->mobile_image);
                }

                $updateData['mobile_image'] =
                    $data['mobile_image']->store('banners', 'public');
            }

            $banner->update($updateData);

            return $banner;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | PUBLISH
    |--------------------------------------------------------------------------
    */
    public function publish(MarketingBanner $banner): MarketingBanner
    {
        if (!$this->canBePublished($banner)) {
            throw new \Exception('Banner tidak valid untuk dipublish.');
        }

        $banner->update([
            'status' => 'published',
            'is_active' => true
        ]);

        return $banner;
    }

    /*
    |--------------------------------------------------------------------------
    | ARCHIVE
    |--------------------------------------------------------------------------
    */
    public function archive(MarketingBanner $banner): MarketingBanner
    {
        $banner->update([
            'status' => 'archived',
            'is_active' => false
        ]);

        return $banner;
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function delete(MarketingBanner $banner): void
    {
        DB::transaction(function () use ($banner) {

            if ($banner->image &&
                Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }

            if ($banner->mobile_image &&
                Storage::disk('public')->exists($banner->mobile_image)) {
                Storage::disk('public')->delete($banner->mobile_image);
            }

            $banner->delete();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | REORDER
    |--------------------------------------------------------------------------
    */
    public function reorder(array $orders): void
    {
        foreach ($orders as $id => $order) {
            MarketingBanner::where('id', $id)
                ->update(['sort_order' => $order]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | TRACK IMPRESSION
    |--------------------------------------------------------------------------
    */
    public function trackImpression(MarketingBanner $banner): void
    {
        $banner->increment('impressions');
    }

    /*
    |--------------------------------------------------------------------------
    | TRACK CLICK
    |--------------------------------------------------------------------------
    */
    public function trackClick(MarketingBanner $banner): void
    {
        $banner->increment('clicks');
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION BEFORE PUBLISH
    |--------------------------------------------------------------------------
    */
    protected function canBePublished(MarketingBanner $banner): bool
    {
        if (!$banner->image) {
            return false;
        }

        if ($banner->start_date &&
            $banner->end_date &&
            $banner->start_date > $banner->end_date) {
            return false;
        }

        return true;
    }
}