<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = [
            'XXS', 'XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL',
            '30', '31', '32', '33', '34', '35', '36', '37', '38',
            '39', '40', '41', '42', '43', '44', '45', '46', '47'
        ];

        foreach ($sizes as $size) {
            DB::table('sizes')->insert([
                'title' => $size,
                'is_active' => true,
            ]);
        }
    }
}
