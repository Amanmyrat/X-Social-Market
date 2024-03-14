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
    public function list(int $limit, ?string $search_query = null, array $conditions = [], array $relationsCount = [], ?string $sort = null): LengthAwarePaginator
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

        // Sorting logic
        if (! is_null($sort) && $sort != 'default') {
            // Initialize sorting direction based on the presence of "-" prefix
            $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';

            // If sorting by 'is_active', adjust direction for natural boolean order preference
            if (ltrim($sort, '-') == 'is_active') {
                $direction = $direction === 'asc' ? 'desc' : 'asc'; // Flip direction
            }

            // Remove "-" prefix from sort parameter if present
            $sort = ltrim($sort, '-');

            // Check for direct attribute sorting
            if (in_array($sort, ['title', 'is_active', 'created_at'])) {
                $query->orderBy($sort, $direction);
            } else {
                // Handling sorting by relationship count
                // Convert sort parameter to expected relation method name format
                $relation = str_replace(['_', 'count'], '', $sort); // e.g., "post_reports_count" to "postReports"
                $camelCaseRelation = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $relation))));

                if (in_array($camelCaseRelation, $relationsCount)) {
                    $query->orderBy($sort, $direction);
                }
            }
        } else {
            // Default sorting (latest first)
            $query->latest();
        }

        return $query->paginate($limit);
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
