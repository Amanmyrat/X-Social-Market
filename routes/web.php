<?php

use App\Models\Category;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {

    $categories = [
        ['title' => 'Sungat', 'icon' => 'sungat.png'],
        ['title' => 'Elektronika', 'icon' => 'elektronika.png'],
        ['title' => 'Aýal-gyzlar geýim', 'icon' => 'ayal_gyzlar_geyim.png'],
        ['title' => 'Erkek geýim', 'icon' => 'erkek_geyim.png'],
        ['title' => 'Çaga geýim', 'icon' => 'caga_geyim.png'],
        ['title' => 'Emläk', 'icon' => 'emlak.png'],
        ['title' => 'Saz gurallary', 'icon' => 'saz_gurallary.png'],
        ['title' => 'Haýwanat', 'icon' => 'haywanat.png'],
        ['title' => 'Sport', 'icon' => 'sport.png']
    ];

    foreach ($categories as $data) {
        $category = Category::create([
            'title' => $data['title'],
            'description' => '',
            'is_active' => true,
            'has_product' => in_array($data['title'], ['Aýal-gyzlar geýim', 'Erkek geýim', 'Çaga geýim'])
        ]);


    }

//    return $iconPath;
    return view('welcome');
});
