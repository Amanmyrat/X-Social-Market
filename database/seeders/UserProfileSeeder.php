<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UserProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::disableQueryLog();
        $locationIds = DB::table('locations')->pluck('id')->all();
        $categoryIds = DB::table('categories')->pluck('id')->all();
        $faker = Faker::create();

        DB::table('users')->orderBy('id')->chunk(200, function ($users) use ($faker, $locationIds, $categoryIds) {
            $userProfiles = [];
            foreach ($users as $user) {
                $isSeller = $user->type == 'seller';
                $userProfiles[] = [
                    'user_id' => $user->id,
                    'full_name' => $faker->name,
                    'bio' => $faker->boolean(70) ? $faker->text : null,
                    'location_id' => $isSeller ? $faker->randomElement($locationIds) : null,
                    'category_id' => $isSeller ? $faker->randomElement($categoryIds) : null,
                    'website' => $isSeller && $faker->boolean(70) ? $faker->domainName : null,
                    'birthdate' => $faker->dateTimeBetween('-50 years', '-16 years')->format('Y-m-d'),
                    'gender' => $faker->randomElement(['male', 'female']),
                    'payment_available' => $isSeller ? $faker->boolean : false,
                    'verified' => $isSeller ? $faker->boolean : false,
                    'private' => $faker->boolean,
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                ];
            }
            DB::table('user_profiles')->insert($userProfiles);
        });

    }
}
