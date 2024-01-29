<?php

namespace App\Services;

use App\Models\Location;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LocationService
{
    /**
     * Create location
     *
     * @param array $data
     */
    public function create(array $data): void
    {
        Location::create($data);
    }

    /**
     * Get location list
     * @param int $limit
     * @param string|null $search_query
     * @return LengthAwarePaginator
     */
    public function list(int $limit, string $search_query = null): LengthAwarePaginator
    {
        return Location::when(isset($search_query), function ($query) use ($search_query) {
                $search_query = '%' . $search_query . '%';
                return $query->where('title', 'LIKE', $search_query);
            })->latest()->paginate($limit);
    }

    /**
     * Update location
     *
     * @param Location $location
     * @param array $data
     * @return Location
     */
    public function update(Location $location, array $data): Location
    {
        $location->update($data);

        return $location;
    }
}
