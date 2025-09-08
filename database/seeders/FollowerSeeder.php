<?php

namespace Database\Seeders;

use App\Models\User;
use DB;
use Illuminate\Database\Seeder;
use Throwable;

class FollowerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @throws Throwable
     */
    public function run(): void
    {
        DB::disableQueryLog();

        $allUserIds = User::pluck('id')->all();

        $totalUsersCount = User::count();
        User::with('profile')->chunk(1000, function ($users) use ($allUserIds, $totalUsersCount) {
            $followersData = [];
            $followRequestsData = [];

            foreach ($users as $user) {
                $isSeller = $user->type === User::TYPE_SELLER;
                $maxFollowers = $isSeller ? 100 : 25;
                $followerCount = min($maxFollowers, $totalUsersCount - 1);

                $availableUserIds = array_diff($allUserIds, [$user->id]);

                $randomUserIds = (array) array_rand(array_flip($availableUserIds), $followerCount);

                foreach ($randomUserIds as $followerId) {
                    $isPrivate = $user->profile->private ?? false;
                    if (! $isPrivate || rand(0, 100) < 90) {
                        $followersData[] = [
                            'follow_user_id' => $followerId,
                            'user_id' => $user->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    } else {
                        $followRequestsData[] = [
                            'following_user_id' => $followerId,
                            'followed_user_id' => $user->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }
            if (! empty($followersData)) {
                foreach (array_chunk($followersData, 5000) as $chunk) {
                    DB::table('followers')->insert($chunk);
                }
            }

            if (! empty($followRequestsData)) {
                foreach (array_chunk($followRequestsData, 5000) as $chunk) {
                    DB::table('follow_requests')->insert($chunk);
                }
            }
        });

    }
}
