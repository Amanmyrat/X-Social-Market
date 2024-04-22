<?php

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\User;
use DB;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::disableQueryLog();
        $users = User::where('type', User::TYPE_USER)->limit(10)->get();
        $sellers = User::where('type', User::TYPE_SELLER)->get();
        $postIds = DB::table('posts')->pluck('id')->all();
        $faker = Faker::create();

        $users->each(function ($user) use ($sellers, $faker, $postIds) {
            $chatsCount = rand(20, 100);
            $userSellers = $sellers->random($chatsCount);

            $uniqueChats = collect();
            $userSellers->each(function ($seller) use ($user, $uniqueChats, $faker, $postIds) {
                $uniqueChats->push([
                    'sender_user_id' => $user->id,
                    'receiver_user_id' => $seller->id,
                    'post_id' => $faker->boolean(30)
                        ? $faker->randomElement($postIds) : null,
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            });

            $uniqueChats = $uniqueChats->unique(function ($chat) {
                return $chat['sender_user_id'] . '-' . $chat['receiver_user_id'];
            });

            Chat::insert($uniqueChats->toArray());
        });

    }
}
