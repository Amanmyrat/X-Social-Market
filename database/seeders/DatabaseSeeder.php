<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            AdminSeeder::class,
            LocationSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            ColorSeeder::class,
            SizeSeeder::class,
            ReportTypeSeeder::class,
            UserSeeder::class,
            UserProfileSeeder::class,
            FollowerSeeder::class,
            PostSeeder::class,
            ProductSeeder::class,
            PostEngagementSeeder::class,
            ChatSeeder::class,
            MessageSeeder::class,
            StorySeeder::class,
        ]);
    }
}
