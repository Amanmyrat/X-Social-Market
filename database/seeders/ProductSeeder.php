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
            ->orderBy('id')->chunk(5000, function ($posts) use ($faker, $brandIds, $colorIds, $sizeIds) {
                $products = [];
                $createdAt = now()->toDateTimeString();
                foreach ($posts as $post) {
                    $productData = [
                        'post_id' => $post->id,
                        'brand_id' => $faker->randomElement($brandIds),
                        'gender' => $faker->randomElement(['male', 'female']),
                        'options' => [],
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];
                    $numColors = $faker->numberBetween(1, 3);
                    for ($j = 0; $j < $numColors; $j++) {
                        $color_id = $faker->randomElement($colorIds);
                        $numSizes = $faker->numberBetween(1, 3);
                        $sizes = [];
                        for ($k = 0; $k < $numSizes; $k++) {
                            $sizes[] = [
                                'size_id' => $faker->randomElement($sizeIds),
                                'price' => $faker->numberBetween(10, 500),
                                'stock' => $faker->numberBetween(0, 100),
                            ];
                        }
                        $productData['options']['colors'][] = [
                            'color_id' => $color_id,
                            'sizes' => $sizes,
                        ];
                    }
                    $productData['options'] = json_encode($productData['options']);
                    $products[] = $productData;
                }
                Product::insert($products);

            });
    }
}
