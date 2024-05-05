<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['title' => 'Sungat', 'icon' => 'sungat.png'],
            ['title' => 'Elektronika', 'icon' => 'elektronika.png'],
            ['title' => 'Aýal-gyzlar geýim', 'icon' => 'ayal_gyzlar_geyim.png'],
            ['title' => 'Erkek geýim', 'icon' => 'erkek_geyim.png'],
            ['title' => 'Çaga geýim', 'icon' => 'caga_geyim.png'],
            ['title' => 'Emläk', 'icon' => 'emlak.png'],
            ['title' => 'Saz gurallary', 'icon' => 'saz_gurallary.png'],
            ['title' => 'Haýwanat', 'icon' => 'haywanat.png'],
            ['title' => 'Sport', 'icon' => 'sport.png'],
        ];

        foreach ($categories as $data) {
            $category = Category::create([
                'title' => $data['title'],
                'description' => '',
                'is_active' => true,
                'has_product' => in_array($data['title'], ['Aýal-gyzlar geýim', 'Erkek geýim', 'Çaga geýim']),
            ]);

            $category->addMediaFromDisk('/category_icons/'.$data['icon'], 'seeders')->toMediaCollection('category_images');

        }
    }
}
