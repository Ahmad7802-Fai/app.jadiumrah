<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\CompanyProfile;
use App\Models\Gallery;
use App\Models\PaketUmrah;
use App\Models\Team;
use App\Models\Testimoni;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicContentController extends Controller
{
    public function site(): JsonResponse
    {
        $profile = CompanyProfile::active()->first();

        return response()->json([
            'success' => true,
            'data' => $profile ? [
                'name' => $profile->name,
                'brand_name' => $profile->brand_name,
                'logo_url' => $this->mediaUrl($profile->logo),
                'email' => $profile->email,
                'phone' => $profile->phone,
                'website' => $profile->website,
                'address' => $profile->address,
                'city' => $profile->city,
                'province' => $profile->province,
                'postal_code' => $profile->postal_code,
            ] : null,
        ]);
    }

    public function packages(Request $request): JsonResponse
    {
        $input = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $query = PaketUmrah::query()
            ->where('is_active', 1)
            ->where('status', 'Aktif');

        if ($search = trim((string) ($input['search'] ?? ''))) {
            $query->where(function ($query) use ($search) {
                $query
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('seo_title', 'like', "%{$search}%");
            });
        }

        $data = $query
            ->latest('created_at')
            ->paginate($input['limit'] ?? 20)
            ->withQueryString();

        return $this->paginated(
            $data,
            fn (PaketUmrah $paket) => $this->packagePayload($paket)
        );
    }

    public function package(string $slug): JsonResponse
    {
        $paket = PaketUmrah::query()
            ->where('slug', $slug)
            ->where('is_active', 1)
            ->where('status', 'Aktif')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $this->packagePayload($paket),
        ]);
    }

    public function news(Request $request): JsonResponse
    {
        $input = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable', 'string', 'max:100'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $query = Berita::query();

        if ($search = trim((string) ($input['search'] ?? ''))) {
            $query->where(function ($query) use ($search) {
                $query
                    ->where('judul', 'like', "%{$search}%")
                    ->orWhere('konten', 'like', "%{$search}%");
            });
        }

        if ($category = trim((string) ($input['category'] ?? ''))) {
            $query->where('kategori', $category);
        }

        $data = $query
            ->latest('created_at')
            ->paginate($input['limit'] ?? 20)
            ->withQueryString();

        return $this->paginated(
            $data,
            fn (Berita $berita) => $this->newsPayload($berita)
        );
    }

    public function newsDetail(string $slug): JsonResponse
    {
        $berita = Berita::query()
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $this->newsPayload($berita),
        ]);
    }

    public function gallery(Request $request): JsonResponse
    {
        $input = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable', 'string', 'max:100'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $query = Gallery::query();

        if ($search = trim((string) ($input['search'] ?? ''))) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($category = trim((string) ($input['category'] ?? ''))) {
            $query->where('category', $category);
        }

        $data = $query
            ->latest('created_at')
            ->paginate($input['limit'] ?? 20)
            ->withQueryString();

        return $this->paginated(
            $data,
            fn (Gallery $gallery) => [
                'id' => (int) $gallery->id,
                'title' => $gallery->title,
                'category' => $gallery->category,
                'photo_url' => $this->mediaUrl($gallery->photo),
            ]
        );
    }

    public function team(Request $request): JsonResponse
    {
        $input = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $query = Team::query();

        if ($search = trim((string) ($input['search'] ?? ''))) {
            $query->where(function ($query) use ($search) {
                $query
                    ->where('nama', 'like', "%{$search}%")
                    ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        $data = $query
            ->orderBy('id')
            ->paginate($input['limit'] ?? 20)
            ->withQueryString();

        return $this->paginated(
            $data,
            fn (Team $team) => [
                'id' => (int) $team->id,
                'name' => $team->nama,
                'position' => $team->jabatan,
                'photo_url' => $this->mediaUrl($team->photo),
                'description' => $team->deskripsi,
            ]
        );
    }

    public function testimonials(Request $request): JsonResponse
    {
        $input = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $query = Testimoni::query();

        if ($search = trim((string) ($input['search'] ?? ''))) {
            $query->where(function ($query) use ($search) {
                $query
                    ->where('nama', 'like', "%{$search}%")
                    ->orWhere('pesan', 'like', "%{$search}%");
            });
        }

        $data = $query
            ->latest('created_at')
            ->paginate($input['limit'] ?? 20)
            ->withQueryString();

        return $this->paginated(
            $data,
            fn (Testimoni $testimoni) => [
                'id' => (int) $testimoni->id,
                'name' => $testimoni->nama,
                'message' => $testimoni->pesan,
                'photo_url' => $this->mediaUrl($testimoni->photo),
                'rating' => $testimoni->rating !== null
                    ? (int) $testimoni->rating
                    : null,
            ]
        );
    }

    private function packagePayload(PaketUmrah $paket): array
    {
        return [
            'id' => (int) $paket->id,
            'title' => $paket->title,
            'slug' => $paket->slug,
            'seo_title' => $paket->seo_title,
            'departure_date' => $paket->tglberangkat
                ? substr((string) $paket->tglberangkat, 0, 10)
                : null,
            'airline' => $paket->pesawat,
            'flight' => $paket->flight,
            'duration_days' => (int) $paket->durasi,
            'seat' => (int) $paket->seat,
            'hotels' => [
                'makkah' => [
                    'name' => $paket->hotmekkah,
                    'rating' => (int) $paket->rathotmekkah,
                ],
                'madinah' => [
                    'name' => $paket->hotmadinah,
                    'rating' => (int) $paket->rathotmadinah,
                ],
            ],
            'prices' => [
                'quad' => (int) $paket->quad,
                'triple' => (int) $paket->triple,
                'double' => (int) $paket->double,
            ],
            'itinerary' => $paket->itin,
            'photo_url' => $this->mediaUrl($paket->photo),
            'extras' => [
                'thaif' => $paket->thaif,
                'dubai' => $paket->dubai,
                'train' => $paket->kereta,
            ],
            'description' => $paket->deskripsi,
            'allow_self_register' => (bool) $paket->allow_self_register,
        ];
    }

    private function newsPayload(Berita $berita): array
    {
        return [
            'id' => (int) $berita->id,
            'title' => $berita->judul,
            'slug' => $berita->slug,
            'content' => $berita->konten,
            'category' => $berita->kategori,
            'thumbnail_url' => $this->mediaUrl($berita->thumbnail),
            'published_at' => $berita->created_at?->toISOString(),
            'updated_at' => $berita->updated_at?->toISOString(),
        ];
    }

    private function mediaUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    private function paginated($paginator, callable $transform): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => array_map($transform, $paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
            'links' => [
                'previous' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ]);
    }
}
