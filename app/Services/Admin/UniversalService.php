<?php

namespace App\Services\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @template T of \Illuminate\Database\Eloquent\Model
 *
 * @property T $model
 */
class UniversalService
{
    protected Model $model;

    /**
     * Set the model for the service to operate on.
     */
    public function setModel(Model $model): void
    {
        $this->model = $model;
    }

    /**
     * Create a new instance of the model.
     */
    public function create(array $data): void
    {
        $this->model->create($data);
    }

    /**
     * /**
     * Get a list of model instances with optional search query and filtering.
     *
     * @param  string|null  $search_query  Optional search query for filtering.
     * @param  array  $conditions  Optional conditions for additional filtering.
     * @param  array  $relationsCount  Optional conditions for additional relations count.
     */
    public function list(int $limit, ?string $search_query = null, array $conditions = [], array $relationsCount = []): LengthAwarePaginator
    {
        $query = $this->model::query();

        $query->withCount($relationsCount);

        // Apply conditions
        foreach ($conditions as $field => $value) {
            $query->where($field, $value);
        }

        // Apply search query if provided
        if (! is_null($search_query)) {
            $search_query = '%'.$search_query.'%';
            $query->where('title', 'LIKE', $search_query);
        }

        return $query->latest()->paginate($limit);
    }

    /**
     * Update a model instance.
     */
    public function update(Model $instance, array $data): Model
    {
        $instance->update($data);

        return $instance;
    }

    /**
     * Delete entities by IDs.
     *
     * @param  array  $ids  Array of entity IDs to delete.
     */
    public function delete(array $ids): void
    {
        $this->model::whereIn('id', $ids)->delete();
    }
}
