<?php

namespace Tests\Feature;

use App\Models\Berita;
use App\Models\CompanyProfile;
use App\Models\Gallery;
use App\Models\PaketUmrah;
use App\Models\Team;
use App\Models\Testimoni;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class PublicApiV1Test extends TestCase
{
    use DatabaseTransactions;

    public function test_package_index_exposes_only_active_packages_with_stable_contract(): void
    {
        $marker = 'api-' . Str::lower(Str::random(12));

        $active = $this->createPackage([
            'title' => "Active {$marker}",
            'slug' => "active-{$marker}",
        ]);

        $this->createPackage([
            'title' => "Inactive {$marker}",
            'slug' => "inactive-{$marker}",
            'status' => 'Tidak Aktif',
            'is_active' => 0,
        ]);

        $response = $this->getJson(
            '/api/v1/paket-umrah?search=' . urlencode($marker)
        );

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.id', $active->id)
            ->assertJsonPath('data.0.title', $active->title)
            ->assertJsonPath('data.0.prices.quad', 25000000)
            ->assertJsonStructure([
                'success',
                'data' => [[
                    'id',
                    'title',
                    'slug',
                    'seo_title',
                    'departure_date',
                    'airline',
                    'flight',
                    'duration_days',
                    'seat',
                    'hotels' => [
                        'makkah' => ['name', 'rating'],
                        'madinah' => ['name', 'rating'],
                    ],
                    'prices' => ['quad', 'triple', 'double'],
                    'itinerary',
                    'photo_url',
                    'extras' => ['thaif', 'dubai', 'train'],
                    'description',
                    'allow_self_register',
                ]],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
                'links' => [
                    'previous',
                    'next',
                ],
            ]);
    }

    public function test_package_detail_returns_active_package_and_hides_inactive_package(): void
    {
        $active = $this->createPackage();

        $inactive = $this->createPackage([
            'status' => 'Tidak Aktif',
            'is_active' => 0,
        ]);

        $this->getJson('/api/v1/paket-umrah/' . $active->slug)
            ->assertOk()
            ->assertJsonPath('data.slug', $active->slug);

        $this->getJson('/api/v1/paket-umrah/' . $inactive->slug)
            ->assertNotFound();
    }

    public function test_news_supports_search_category_and_slug_detail(): void
    {
        $marker = 'api-' . Str::lower(Str::random(12));

        $berita = Berita::create([
            'judul' => "Berita {$marker}",
            'slug' => "berita-{$marker}",
            'konten' => "Konten {$marker}",
            'kategori' => 'Umrah',
            'thumbnail' => null,
        ]);

        Berita::create([
            'judul' => "Lain {$marker}",
            'slug' => "lain-{$marker}",
            'konten' => "Konten {$marker}",
            'kategori' => 'Travel',
            'thumbnail' => null,
        ]);

        $this->getJson(
            '/api/v1/berita?search=' . urlencode($marker) . '&category=Umrah'
        )
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.slug', $berita->slug);

        $this->getJson('/api/v1/berita/' . $berita->slug)
            ->assertOk()
            ->assertJsonPath('data.title', $berita->judul);
    }

    public function test_site_exposes_only_public_company_profile_fields(): void
    {
        CompanyProfile::query()->update(['is_active' => false]);

        CompanyProfile::create([
            'name' => 'JadiUmrah API Test',
            'brand_name' => 'JadiUmrah',
            'email' => 'public@example.test',
            'phone' => '0800000000',
            'website' => 'https://example.test',
            'address' => 'Jakarta',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'postal_code' => '10000',
            'npwp' => 'SHOULD-NOT-BE-PUBLIC',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/site');

        $response
            ->assertOk()
            ->assertJsonPath('data.name', 'JadiUmrah API Test')
            ->assertJsonMissingPath('data.npwp')
            ->assertJsonMissingPath('data.bank_account_number');
    }

    public function test_gallery_team_and_testimonials_have_explicit_public_shapes(): void
    {
        $marker = Str::lower(Str::random(12));

        $gallery = Gallery::create([
            'title' => "Gallery {$marker}",
            'photo' => null,
            'category' => 'Umrah',
        ]);

        $team = Team::create([
            'nama' => "Team {$marker}",
            'jabatan' => 'Director',
            'photo' => null,
            'deskripsi' => 'Profile',
        ]);

        $testimonial = Testimoni::create([
            'nama' => "Jamaah {$marker}",
            'pesan' => 'Pelayanan baik',
            'photo' => null,
            'rating' => 5,
        ]);

        $this->getJson('/api/v1/gallery?search=' . urlencode($marker))
            ->assertOk()
            ->assertJsonPath('data.0.id', $gallery->id)
            ->assertJsonPath('data.0.title', $gallery->title);

        $this->getJson('/api/v1/team?search=' . urlencode($marker))
            ->assertOk()
            ->assertJsonPath('data.0.id', $team->id)
            ->assertJsonPath('data.0.name', $team->nama);

        $this->getJson('/api/v1/testimoni?search=' . urlencode($marker))
            ->assertOk()
            ->assertJsonPath('data.0.id', $testimonial->id)
            ->assertJsonPath('data.0.rating', 5);
    }

    public function test_public_client_search_endpoint_is_not_exposed(): void
    {
        $this->getJson('/api/clients/search?q=test')
            ->assertNotFound();
    }

    public function test_public_query_limits_are_validated(): void
    {
        $this->getJson('/api/v1/paket-umrah?limit=101')
            ->assertUnprocessable();

        $this->getJson('/api/v1/berita?page=0')
            ->assertUnprocessable();
    }

    private function createPackage(array $overrides = []): PaketUmrah
    {
        $token = Str::lower(Str::random(12));

        return PaketUmrah::create(array_merge([
            'title' => "Paket {$token}",
            'slug' => "paket-{$token}",
            'seo_title' => "Paket {$token}",
            'tglberangkat' => '2030-01-10',
            'pesawat' => 'Test Airline',
            'flight' => 'TEST-001',
            'durasi' => 9,
            'seat' => 40,
            'hotmekkah' => 'Hotel Makkah',
            'rathotmekkah' => 5,
            'hotmadinah' => 'Hotel Madinah',
            'rathotmadinah' => 4,
            'quad' => 25000000,
            'triple' => 27000000,
            'double' => 30000000,
            'itin' => 'Test itinerary',
            'photo' => 'paket/api-test.jpg',
            'thaif' => 'Tidak',
            'dubai' => 'Tidak',
            'kereta' => 'Tidak',
            'deskripsi' => 'Test description',
            'status' => 'Aktif',
            'is_active' => 1,
            'allow_self_register' => 1,
        ], $overrides));
    }
}
