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
            $this->errorMessage = 'Önüm hökmanydyr we sanaw görnüşinde bolmalydyr.';

            return false;
        }

        if (! isset($value['brand_id']) || ! Brand::where('id', $value['brand_id'])->exists()) {
            $this->errorMessage = 'Saýlanan önüm markasy nädogrydyr.';

            return false;
        }

        if (! isset($value['gender']) || ! in_array($value['gender'], ['male', 'female'])) {
            $this->errorMessage = 'Önüm jynsy diňe "erkek" ýa-da "aýal" bolup biler.';

            return false;
        }

        if (! isset($value['options']) || ! is_array($value['options'])) {
            $this->errorMessage = 'Önüm opsiýalary sanaw görnüşinde bolmalydyr.';

            return false;
        }

        $option = $value['options'];
        if (! isset($option['colors']) || ! is_array($option['colors'])) {
            $this->errorMessage = 'Önüm reňkleri sanaw görnüşinde bolmalydyr.';

            return false;
        }
        foreach ($option['colors'] as $color) {
            if (! isset($color['color_id']) || ! Color::where('id', $color['color_id'])->exists()) {
                $this->errorMessage = 'Saýlanan önüm reňki nädogrydyr.';

                return false;
            }
            if (! isset($color['sizes']) || ! is_array($color['sizes'])) {
                $this->errorMessage = 'Önüm ölçegleri sanaw görnüşinde bolmalydyr.';

                return false;
            }
            foreach ($color['sizes'] as $size) {
                if (! isset($size['size_id']) || ! Size::where('id', $size['size_id'])->exists()) {
                    $this->errorMessage = 'Saýlanan önüm ölçegi nädogrydyr.';

                    return false;
                }
                if (! isset($size['price']) || ! is_numeric($size['price']) || $size['price'] < 0) {
                    $this->errorMessage = 'Önüm bahasy 0-dan kiçi bolmadyk san bolmalydyr.';

                    return false;
                }
                if (! isset($size['stock']) || ! is_numeric($size['stock']) || $size['stock'] < 0) {
                    $this->errorMessage = 'Önüm stogy 0-dan kiçi bolmadyk san bolmalydyr.';

                    return false;
                }
            }
        }

        return true;
    }

    public function message(): string
    {
        return $this->errorMessage ?: 'Önüm maglumatlary nädogrydyr.';
    }
}
