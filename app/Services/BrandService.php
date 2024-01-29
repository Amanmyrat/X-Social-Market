<?php

namespace App\Services;

use App\Models\Brand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BrandService
{
    /**
     * Create brand
     *
     * @param array $data
     */
    public function create(array $data): void
    {
        Brand::create($data);
    }

    /**
     * Get brand list
     * @param string $type
     * @param int $limit
     * @param string|null $search_query
     * @return LengthAwarePaginator
     */
    public function list(string $type, int $limit, string $search_query = null): LengthAwarePaginator
    {
        return Brand::where('type', $type)->when(isset($search_query), function ($query) use ($search_query) {
                $search_query = '%' . $search_query . '%';
                return $query->where('title', 'LIKE', $search_query);
            })->latest()->paginate($limit);
    }

    /**
     * Update brand
     *
     * @param Brand $brand
     * @param array $data
     * @return Brand
     */
    public function update(Brand $brand, array $data): Brand
    {
        $brand->update($data);

        return $brand;
    }
}
