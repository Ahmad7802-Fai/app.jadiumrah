<?php

namespace App\Services\Visa;

use App\Models\VisaProduct;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class VisaProductService
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return VisaProduct::query()
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', function ($query) use ($filters) {
                $query->where('is_active', (bool) $filters['is_active']);
            })
            ->when(!empty($filters['product_type']), function ($query) use ($filters) {
                $query->where('product_type', $filters['product_type']);
            })
            ->when(!empty($filters['keyword']), function ($query) use ($filters) {
                $keyword = trim($filters['keyword']);
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('code', 'like', "%{$keyword}%")
                        ->orWhere('slug', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getActiveProducts(): Collection
    {
        return VisaProduct::query()
            ->active()
            ->ordered()
            ->get();
    }

    public function findById(int $id): VisaProduct
    {
        return VisaProduct::query()->findOrFail($id);
    }

    public function create(array $data): VisaProduct
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['code'] = $data['code'] ?? $this->generateCode($data['name']);

        return VisaProduct::query()->create($data);
    }

    public function update(VisaProduct $product, array $data): VisaProduct
    {
        if (!empty($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $product->update($data);

        return $product->fresh();
    }

    public function delete(VisaProduct $product): bool
    {
        return (bool) $product->delete();
    }

    public function toggleActive(VisaProduct $product): VisaProduct
    {
        $product->update([
            'is_active' => !$product->is_active,
        ]);

        return $product->fresh();
    }

    protected function generateCode(string $name): string
    {
        $base = strtoupper(Str::slug(Str::limit($name, 20, ''), ''));
        $random = str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);

        return 'VSP-' . $base . '-' . $random;
    }
}