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
        return [
            'limit' => ['filled', 'integer'],
            'search_query' => ['filled', 'string'],
            'type' => ['filled', 'in:simple,clothing'],
            'sort' => ['filled', 'string', 'in:default,
                        title,-title,
                        is_active,-is_active,
                        products_count,-products_count'
            ],
        ];
    }
}
