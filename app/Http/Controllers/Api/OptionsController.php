<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Admin\LocationResource;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ColorResource;
use App\Http\Resources\SizeResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Location;
use App\Models\Size;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OptionsController extends ApiBaseController
{
    /**
     * Categories list
     */
    public function categories(): AnonymousResourceCollection
    {
        $categories = Category::where('is_active', true)->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Locations list
     */
    public function locations(): AnonymousResourceCollection
    {
        $locations = Location::where('is_active', true)->get();

        return LocationResource::collection($locations);
    }

    /**
     * Brands list
     */
    public function brands(): AnonymousResourceCollection
    {
        $brands = Brand::where('is_active', true)->get();

        return BrandResource::collection($brands);
    }

    /**
     * Colors list
     */
    public function colors(): AnonymousResourceCollection
    {
        $colors = Color::where('is_active', true)->get();

        return ColorResource::collection($colors);
    }

    /**
     * Sizes list
     */
    public function sizes(): AnonymousResourceCollection
    {
        $sizes = Size::where('is_active', true)->get();

        return SizeResource::collection($sizes);
    }
}
