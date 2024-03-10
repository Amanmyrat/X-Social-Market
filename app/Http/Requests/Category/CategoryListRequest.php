<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class CategoryListRequest extends FormRequest
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
            'posts_count',
            '-posts_count',
        ];
        $allowedSortOptionsString = implode(',', $allowedSortOptions);

        return [
            'limit' => ['filled', 'integer'],
            'search_query' => ['filled', 'string'],
            'sort' => ['filled', 'string', 'in:' . $allowedSortOptionsString
            ],
        ];
    }
}
