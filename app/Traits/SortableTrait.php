<?php

namespace App\Traits;

trait SortableTrait
{
    protected function applySorting($query, ?string $sort, array $sortableFields): void
    {
        if (! is_null($sort) && $sort != 'default') {
            $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
            $sort = ltrim($sort, '-');

            if ($sort == 'is_active') {
                $direction = $direction === 'asc' ? 'desc' : 'asc';
            }

            if (in_array($sort, $sortableFields)) {
                $query->orderBy($sort, $direction);
            }
        } else {
            $query->latest();
        }
    }
}
