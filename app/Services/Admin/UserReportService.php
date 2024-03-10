<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserReportService
{
    public function list(int $limit, ?string $search_query = null): LengthAwarePaginator
    {
        return User::withCount(['reportsAgainst as reports_count'])
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
            })
            ->orderByDesc('reports_count')
            ->paginate($limit);
    }

    public function getUsersWhoReportedUser(User $user): Collection
    {
        return $user->reportsAgainst()->with(['reporter.profile', 'reportType'])->get();
    }
}
