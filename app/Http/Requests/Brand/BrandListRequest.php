<?php

namespace App\Http\Requests\Brand;

use Illuminate\Foundation\Http\FormRequest;

class BrandListRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $allowedSortOptions = [
            'default',
            'title',
            '-title',
            'is_active',
            '-is_active',
            'products_count',
            '-products_count',
        ];
        $allowedSortOptionsString = implode(',', $allowedSortOptions);

        return [
            'limit' => ['filled', 'integer'],
            'type' => ['filled', 'in:simple,clothing'],
            'sort' => ['filled', 'string', 'in:'.$allowedSortOptionsString],
        ];
    }

    public function messages(): array
    {
        return [
            'limit.filled' => 'Çäk girizilen bolmalydyr.',
            'limit.integer' => 'Çäk diňe sanlardan durmalydyr.',
            'type.filled' => 'Görnüşi girizilen bolmalydyr.',
            'type.in' => 'Görnüşi diňe "simple" ýa-da "clothing" bolup biler.',
            'sort.filled' => 'Tertipleme girizilen bolmalydyr.',
            'sort.string' => 'Tertipleme dogry görnüşde bolmalydyr.',
            'sort.in' => 'Tertipleme üçin dogry opsiýany saýlaň: default, title, -title, is_active, -is_active, products_count, -products_count.',
        ];
    }

}
