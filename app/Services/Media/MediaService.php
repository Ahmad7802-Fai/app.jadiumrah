<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class MediaService
{
    protected string $disk = 'public';
    protected string $folder = 'pakets';

    /*
    |--------------------------------------------------------------------------
    | UPLOAD IMAGE (FILE → FULL + THUMB)
    |--------------------------------------------------------------------------
    */
    public function uploadImage(UploadedFile $file, ?string $folder = null): array
    {
        $folder = $folder ?? $this->folder;
        $name = Str::uuid();

        // FULL
        $fullPath = "{$folder}/{$name}.webp";

        $image = Image::make($file)
            ->encode('webp', 80);

        Storage::disk($this->disk)->put($fullPath, $image);

        // THUMB
        $thumbPath = "{$folder}/thumb_{$name}.webp";

        $thumb = Image::make($file)
            ->resize(400, null, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            })
            ->encode('webp', 70);

        Storage::disk($this->disk)->put($thumbPath, $thumb);

        return [
            'full' => $fullPath,
            'thumb' => $thumbPath,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | UPLOAD SINGLE FILE (GALLERY)
    |--------------------------------------------------------------------------
    */
    public function uploadSingle(UploadedFile $file, ?string $folder = null): string
    {
        $folder = $folder ?? $this->folder;
        $name = Str::uuid();
        $path = "{$folder}/{$name}.webp";

        $image = Image::make($file)
            ->encode('webp', 80);

        Storage::disk($this->disk)->put($path, $image);

        return $path;
    }

    /*
    |--------------------------------------------------------------------------
    | BASE64 → FULL + THUMB
    |--------------------------------------------------------------------------
    */
    public function uploadBase64Image(string $base64, ?string $folder = null): array
    {
        $folder = $folder ?? $this->folder;
        $name = Str::uuid();

        $binary = $this->decodeBase64($base64);

        // FULL
        $fullPath = "{$folder}/{$name}.webp";

        Storage::disk($this->disk)->put(
            $fullPath,
            Image::make($binary)->encode('webp', 80)
        );

        // THUMB
        $thumbPath = "{$folder}/thumb_{$name}.webp";

        Storage::disk($this->disk)->put(
            $thumbPath,
            Image::make($binary)
                ->resize(400, null, function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                })
                ->encode('webp', 70)
        );

        return [
            'full' => $fullPath,
            'thumb' => $thumbPath,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | BASE64 → SINGLE (GALLERY)
    |--------------------------------------------------------------------------
    */
    public function uploadBase64Single(string $base64, ?string $folder = null): string
    {
        $folder = $folder ?? $this->folder;
        $name = Str::uuid();

        $binary = $this->decodeBase64($base64);

        $path = "{$folder}/{$name}.webp";

        Storage::disk($this->disk)->put(
            $path,
            Image::make($binary)->encode('webp', 80)
        );

        return $path;
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE FILE
    |--------------------------------------------------------------------------
    */
    public function delete(?string $path): void
    {
        if ($path) {
            Storage::disk($this->disk)->delete($path);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE MULTIPLE
    |--------------------------------------------------------------------------
    */
    public function deleteMany(array $paths): void
    {
        foreach ($paths as $path) {
            $this->delete($path);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | GET URL (AUTO CDN + LOCAL FALLBACK)
    |--------------------------------------------------------------------------
    */
    public function url(?string $path): ?string
    {
        if (!$path) return null;

        $cdn = config('app.cdn_url');

        // ✅ Production → pakai CDN
        if ($cdn && app()->environment('production')) {
            return rtrim($cdn, '/') . '/' . ltrim($path, '/');
        }

        // ✅ Local / dev → pakai storage
        return asset('storage/' . $path);
    }

    /*
    |--------------------------------------------------------------------------
    | DECODE BASE64
    |--------------------------------------------------------------------------
    */
    private function decodeBase64(string $base64): string
    {
        if (str_contains($base64, ',')) {
            $base64 = explode(',', $base64)[1];
        }

        return base64_decode($base64);
    }
}