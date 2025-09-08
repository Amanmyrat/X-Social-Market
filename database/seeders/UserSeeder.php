<?php

namespace Database\Seeders;

use App\Models\User;
use DB;
use Exception;
use Faker\Factory as Faker;
use Hash;
use Illuminate\Database\Seeder;
use Throwable;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @throws Exception
     * @throws Throwable
     */
    public function run()
    {
        DB::disableQueryLog();

        $faker = Faker::create();
        $userCount = 10000;
        $users = [];

        $password = Hash::make('12345678');
        for ($i = 0; $i < $userCount; $i++) {
            $createdAt = $faker->dateTimeBetween('-1 year', 'now');
            $updatedAt = $faker->dateTimeBetween($createdAt, 'now');

            $users[] = [
                'phone' => $faker->unique()->numberBetween(61000000, 71000000),
                'username' => 'ulanyjy_'.$faker->unique()->numberBetween(10000000, 99999999),
                'email' => null,
                'password' => $password,
                'type' => $faker->randomElement(['user', 'seller']),
                'device_token' => '',
                'is_active' => true,
                'blocked_at' => null,
                'block_reason' => null,
                'last_activity' => now(),
                'created_at' => $createdAt->format('Y-m-d H:i:s'),
                'updated_at' => $updatedAt->format('Y-m-d H:i:s'),
            ];
        }

        foreach (array_chunk($users, 500) as $chunk) {
            User::upsert($chunk, ['phone'], ['username', 'email', 'password', 'type', 'device_token', 'last_activity', 'created_at', 'updated_at', 'is_active', 'blocked_at', 'block_reason']);
        }
    }
}
