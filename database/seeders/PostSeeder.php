<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use DB;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::disableQueryLog();

        $faker = Faker::create();
        $postCount = 1000;

        $categoryIds = Category::pluck('id')->toArray();
        $userIds = User::where('type', User::TYPE_SELLER)->pluck('id')->toArray();

        $createdAt = now()->toDateTimeString();

        $posts = [];
        for ($i = 0; $i < $postCount; $i++) {
            $category_id = $faker->randomElement($categoryIds);
            $user_id = $faker->randomElement($userIds);

            $posts[] = [
                'category_id' => $category_id,
                'user_id' => $user_id,
                'media_type' => 'image',
                'caption' => 'Example Caption',
                'price' => $faker->numberBetween(10, 1000),
                'description' => 'Example Description',
                'location' => 'Ashgabat',
                'can_comment' => $faker->boolean,
                'is_active' => true,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }

        foreach (array_chunk($posts, 5000) as $chunk) {
            Post::insert($chunk);
        }
    }
}
