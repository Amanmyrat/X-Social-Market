<?php

namespace App\Transformers;

use App\Models\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
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
