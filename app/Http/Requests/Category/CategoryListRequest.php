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
        return [
            'limit' => ['filled', 'integer'],
            'search_query' => ['filled', 'string'],
            'sort' => ['filled', 'string', 'in:default,
                        title,-title,
                        is_active,-is_active,
                        posts_count,-posts_count'
            ],
        ];
    }
}
