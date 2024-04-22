<?php

namespace Database\Seeders;

use App\Models\User;
use DB;
use Exception;
use Hash;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Throwable;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
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
            $users[] = [
                'phone' => $faker->unique()->numberBetween(61000000, 71000000),
                'username' => 'ulanyjy_' . $faker->unique()->numberBetween(10000000, 99999999),
                'email' => null,
                'password' => $password,
                'type' => $faker->randomElement(['user', 'seller']),
                'device_token' => '',
                'last_activity' => now(),
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                'is_active' => true,
                'blocked_at' => null,
                'block_reason' => null,
            ];

        }
        foreach (array_chunk($users, 5000) as $chunk) {
            User::upsert($chunk, ['phone'], ['username', 'email', 'password', 'type', 'device_token', 'last_activity', 'created_at', 'updated_at', 'is_active', 'blocked_at', 'block_reason']);
        }
    }
}
