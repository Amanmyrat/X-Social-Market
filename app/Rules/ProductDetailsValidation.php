<?php

namespace App\Rules;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Contracts\Validation\Rule;

class ProductDetailsValidation implements Rule
{
    protected int $categoryId;

    protected string $errorMessage = '';

    public function __construct(int $categoryId)
    {
        $this->categoryId = $categoryId;
    }

    public function passes($attribute, $value): bool
    {
        $category = Category::find($this->categoryId);

        if (! $category || ! $category->has_product) {
            return true;
        }

        if (! isset($value) || ! is_array($value)) {
            $this->errorMessage = 'Product is required and must be array';

            return false;
        }

        if (! isset($value['brand_id']) || ! Brand::where('id', $value['brand_id'])->exists()) {
            $this->errorMessage = 'The selected product brand is invalid.';

            return false;
        }

        if (! isset($value['gender']) || ! in_array($value['gender'], ['male', 'female'])) {
            $this->errorMessage = 'Product gender must be male or female.';

            return false;
        }

        if (! isset($value['options']) || ! is_array($value['options'])) {
            $this->errorMessage = 'Product options must be an array.';

            return false;
        }

        $option = $value['options'];
        if (! isset($option['colors']) || ! is_array($option['colors'])) {
            $this->errorMessage = 'Product colors must be an array.';

            return false;
        }
        foreach ($option['colors'] as $color) {
            if (! isset($color['color_id']) || ! Color::where('id', $color['color_id'])->exists()) {
                $this->errorMessage = 'The selected product color is invalid.';

                return false;
            }
            if (! isset($color['sizes']) || ! is_array($color['sizes'])) {
                $this->errorMessage = 'Product sizes must be an array.';

                return false;
            }
            foreach ($color['sizes'] as $size) {
                if (! isset($size['size_id']) || ! Size::where('id', $size['size_id'])->exists()) {
                    $this->errorMessage = 'The selected product size is invalid.';

                    return false;
                }
                if (! isset($size['price']) || ! is_numeric($size['price']) || $size['price'] < 0) {
                    $this->errorMessage = 'Product price must be a non-negative number.';

                    return false;
                }
                if (! isset($size['stock']) || ! is_numeric($size['stock']) || $size['stock'] < 0) {
                    $this->errorMessage = 'Product stock must be a non-negative integer.';

                    return false;
                }
            }
        }

        return true;
    }

    public function message(): string
    {
        return $this->errorMessage ?: 'The product information is invalid.';
    }
}
