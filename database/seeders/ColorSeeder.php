<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            ['title' => 'Ak', 'code' => '#FFFFFF'],
            ['title' => 'Gara', 'code' => '#000000'],
            ['title' => 'Gök', 'code' => '#0000FF'],
            ['title' => 'Gyzyl', 'code' => '#FF0000'],
            ['title' => 'Ýaşyl', 'code' => '#00FF00'],
            ['title' => 'Mämişi', 'code' => '#FFA500'],
            ['title' => 'Çal', 'code' => '#808080'],
            ['title' => 'Sary', 'code' => '#FFFF00'],
            ['title' => 'Haki', 'code' => '#808000'],
        ];

        foreach ($colors as $color) {
            DB::table('colors')->insert([
                'title' => $color['title'],
                'code' => $color['code'],
                'is_active' => true,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ]);
        }
    }
}
