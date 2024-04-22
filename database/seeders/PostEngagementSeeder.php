<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\PostBookmark;
use App\Models\PostComment;
use App\Models\PostFavorite;
use App\Models\PostRating;
use App\Models\PostView;
use App\Models\User;
use DB;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class PostEngagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::disableQueryLog();
        $faker = Faker::create();

        $postsIds = Post::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();

        $views = [];
        $favorites = [];
        $comments = [];
        $bookmarks = [];
        $ratings = [];

        foreach ($postsIds as $postId) {
            $numViews = rand(0, 100);
            $numFavorites = rand(0, 25);
            $numComments = rand(0, 3);
            $numBookmarks = rand(0, 5);
            $numRatings = rand(0, 4);

            $createdAt = now()->toDateTimeString();

            for ($i = 0; $i < $numViews; $i++) {
                $views[] = [
                    'user_id' => $faker->randomElement($userIds),
                    'post_id' => $postId,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }

            for ($i = 0; $i < $numFavorites; $i++) {
                $favorites[] = [
                    'user_id' => $faker->randomElement($userIds),
                    'post_id' => $postId,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }

            for ($i = 0; $i < $numComments; $i++) {
                $comments[] = [
                    'parent_id' => 0,
                    'user_id' => $faker->randomElement($userIds),
                    'post_id' => $postId,
                    'comment' => $faker->sentence(),
                    'is_active' => true,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }

            for ($i = 0; $i < $numBookmarks; $i++) {
                $bookmarks[] = [
                    'user_id' => $faker->randomElement($userIds),
                    'post_id' => $postId,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }

            for ($i = 0; $i < $numRatings; $i++) {
                $ratings[] = [
                    'user_id' => $faker->randomElement($userIds),
                    'post_id' => $postId,
                    'rating' => rand(1, 5),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }
        }

        $this->insertInteractions(PostView::class, $views);
        $this->insertInteractions(PostFavorite::class, $favorites);
        $this->insertInteractions(PostComment::class, $comments);
        $this->insertInteractions(PostBookmark::class, $bookmarks);
        $this->insertInteractions(PostRating::class, $ratings);
    }

    private function insertInteractions($modelClass, $data)
    {
        foreach (array_chunk($data, 5000) as $chunk) {
            $modelClass::insert($chunk);
        }
    }
}
