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

        // Validate colors array if present and not empty
        if (isset($value['colors']) && !empty($value['colors'])) {
            if (!is_array($value['colors'])) {
                $this->errorMessage = 'Önüm reňkleri sanaw görnüşinde bolmalydyr.';

                return false;
            }

            foreach ($value['colors'] as $colorId) {
                if (!Color::where('id', $colorId)->exists()) {
                    $this->errorMessage = 'Saýlanan önüm reňki nädogrydyr.';

                    return false;
                }
            }
        }

        // Validate sizes array if present and not empty
        if (isset($value['sizes']) && !empty($value['sizes'])) {
            if (!is_array($value['sizes'])) {
                $this->errorMessage = 'Önüm ölçegleri sanaw görnüşinde bolmalydyr.';

                return false;
            }

            foreach ($value['sizes'] as $sizeId) {
                if (!Size::where('id', $sizeId)->exists()) {
                    $this->errorMessage = 'Saýlanan önüm ölçegi nädogrydyr.';

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
