<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Traits\SortableTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserReportService
{
    use SortableTrait;

    public function list(int $limit, ?string $search_query = null, ?string $sort = null): LengthAwarePaginator
    {
        $query = User::withCount(['reportsAgainst as reports_count'])
            ->with(['profile', 'latestReportAgainst' => function ($query) {
                $query->with(['reportType']);
            }])
            ->has('reportsAgainst')
            ->when($search_query, function ($query) use ($search_query) {
                $search_query = '%'.$search_query.'%';

                return $query->where('username', 'LIKE', $search_query)
                    ->orWhereHas('profile', function ($query) use ($search_query) {
                        $query->where('full_name', 'LIKE', $search_query);
                    });
            });

        if (($sort == 'default')) {
            $query->orderByDesc('reports_count');
        } else {
            $this->applySorting($query, $sort, ['username', 'is_active', 'created_at', 'reports_count']);
        }

        return $query->paginate($limit);
    }

    public function getUsersWhoReportedUser(User $user): Collection
    {
        return $user->reportsAgainst()->with(['reporter.profile', 'reportType'])->get();
    }
}
