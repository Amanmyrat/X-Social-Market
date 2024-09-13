<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use DB;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::disableQueryLog();
        $categoryIdsWithProducts = Category::where('has_product', true)->pluck('id')->toArray();

        $brandIds = Brand::pluck('id')->toArray();
        $colorIds = Color::pluck('id')->toArray();
        $sizeIds = Size::pluck('id')->toArray();
        $faker = Faker::create();

        DB::table('posts')->whereIn('category_id', $categoryIdsWithProducts)
            ->orderBy('id')->chunk(1000, function ($posts) use ($faker, $brandIds, $colorIds, $sizeIds) {
                $products = [];
                $productColorInserts = [];
                $productSizeInserts = [];
                $createdAt = now()->toDateTimeString();

                foreach ($posts as $post) {
                    // Prepare product data
                    $productData = [
                        'post_id' => $post->id,
                        'brand_id' => $faker->randomElement($brandIds),
                        'gender' => $faker->randomElement(['male', 'female']),
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];
                    $products[] = $productData;
                }

                // Bulk insert products
                Product::insert($products);

                // Fetch the last inserted products IDs to link colors and sizes
                $lastInsertedProducts = Product::latest('id')->take(count($products))->get();

                foreach ($lastInsertedProducts as $product) {
                    // Randomly select and prepare color relationships
                    $selectedColorIds = $faker->randomElements($colorIds, $faker->numberBetween(1, 3));
                    foreach ($selectedColorIds as $colorId) {
                        $productColorInserts[] = [
                            'product_id' => $product->id,
                            'color_id' => $colorId,
                        ];
                    }

                    // Randomly select and prepare size relationships
                    $selectedSizeIds = $faker->randomElements($sizeIds, $faker->numberBetween(1, 3));
                    foreach ($selectedSizeIds as $sizeId) {
                        $productSizeInserts[] = [
                            'product_id' => $product->id,
                            'size_id' => $sizeId,
                        ];
                    }
                }

                // Bulk insert color and size relationships
                DB::table('product_color')->insert($productColorInserts);
                DB::table('product_size')->insert($productSizeInserts);
            });

    }
}
