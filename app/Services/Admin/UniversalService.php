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
        if (! is_null($sort)) {
            $direction = 'asc';
            $isDescending = str_starts_with($sort, '-');
            if ($isDescending) {
                $direction = 'desc';
                $sort = substr($sort, 1); // Remove "-" prefix
            }

            // Adjust for boolean fields to reverse the default sort order
            if ($sort === 'is_active' && !$isDescending) {
                // For ascending sort on boolean, flip to descending to get true values first
                $direction = 'desc';
            } else if ($sort === 'is_active' && $isDescending) {
                // For descending sort on boolean, flip to ascending to get false values first
                $direction = 'asc';
            }

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
