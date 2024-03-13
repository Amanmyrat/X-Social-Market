<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class PostListRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $allowedSortOptions = [
            'default',
            'caption',
            '-caption',
            'price',
            '-price',
            'is_active',
            '-is_active',
            'created_at',
            '-created_at',
        ];
        $allowedSortOptionsString = implode(',', $allowedSortOptions);

        return [
            'limit' => ['filled', 'integer'],
            'sort' => ['filled', 'in:'.$allowedSortOptionsString,
            ],
        ];
    }
}
