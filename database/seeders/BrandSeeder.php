<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $simpleBrands = [
            'Apple', 'Samsung', 'Xiaomi', 'Redmi', 'Bosch',
            'JBL', 'Karaca Home', 'Asus', 'Akira'
        ];

        $clothingBrands = [
            'Brioni', 'Lacoste', 'Stefano Ricci', 'Hugo Boss',
            'Zara', 'Defacto', 'Adidas', 'Nike', 'Massimo Dutti'
        ];

        foreach ($simpleBrands as $brand) {
            DB::table('brands')->insert([
                'title' => $brand,
                'type' => 'simple',
                'is_active' => true,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ]);
        }

        foreach ($clothingBrands as $brand) {
            DB::table('brands')->insert([
                'title' => $brand,
                'type' => 'clothing',
                'is_active' => true,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ]);
        }
    }
}
