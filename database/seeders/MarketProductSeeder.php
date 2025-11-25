<?php

namespace Database\Seeders;

use App\Models\MarketProduct;
use Illuminate\Database\Seeder;

class MarketProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'iPhone 15 Pro Max',
                'description' => 'Latest Apple iPhone with amazing features and performance. Brand new, factory sealed.',
                'price_tnt' => 5000.00,
                'stock' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Samsung Galaxy Watch 6',
                'description' => 'Advanced smartwatch with health tracking, GPS, and more. Perfect for fitness enthusiasts.',
                'price_tnt' => 1500.00,
                'stock' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Sony WH-1000XM5 Headphones',
                'description' => 'Industry-leading noise canceling wireless headphones with premium sound quality.',
                'price_tnt' => 1200.00,
                'stock' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'iPad Air M2',
                'description' => 'Powerful tablet with M2 chip, perfect for work and entertainment.',
                'price_tnt' => 3000.00,
                'stock' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'PlayStation 5 Console',
                'description' => 'Next-gen gaming console with stunning graphics and exclusive games.',
                'price_tnt' => 4000.00,
                'stock' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'MacBook Air M3',
                'description' => 'Ultra-thin and powerful laptop with M3 chip. Perfect for students and professionals.',
                'price_tnt' => 6000.00,
                'stock' => 0,
                'is_active' => false,
            ],
            [
                'name' => 'AirPods Pro (2nd generation)',
                'description' => 'Premium wireless earbuds with active noise cancellation.',
                'price_tnt' => 800.00,
                'stock' => 15,
                'is_active' => true,
            ],
            [
                'name' => 'Nintendo Switch OLED',
                'description' => 'Portable gaming console with vibrant OLED screen.',
                'price_tnt' => 1800.00,
                'stock' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Kindle Paperwhite',
                'description' => 'E-reader with adjustable warm light and waterproof design.',
                'price_tnt' => 600.00,
                'stock' => 12,
                'is_active' => true,
            ],
            [
                'name' => 'GoPro HERO12 Black',
                'description' => 'Action camera with 5.3K video and superior stabilization.',
                'price_tnt' => 2000.00,
                'stock' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            MarketProduct::create($product);
        }
    }
}

