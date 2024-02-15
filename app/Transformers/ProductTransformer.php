<?php

namespace App\Transformers;

use App\Models\Post;
use App\Models\Product;
use JetBrains\PhpStorm\ArrayShape;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    #[ArrayShape(['id' => "int", 'brand' => "mixed", 'gender' => "string", 'options' => "array"])]
    public function transform(Product $product): array
    {
        return [
            'id' => $product->id,
            'brand' => $product->brand,
            'gender' => $product->gender,
            'options' => $product->options,
        ];
    }
}
