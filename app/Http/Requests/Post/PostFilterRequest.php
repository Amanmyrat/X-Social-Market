<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class PostFilterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $allowedSortOptions = [
            'default',
            'price',
            '-price',
        ];
        $allowedSortOptionsString = implode(',', $allowedSortOptions);

        return [
            'user_id' => ['filled', 'exists:users,id'],
            'price_min' => ['filled', 'integer'],
            'price_max' => ['filled', 'integer'],

            'brands' => ['filled', 'array'],
            'brands.*' => ['filled', 'int', 'exists:brands,id'],

            'colors' => ['filled', 'array'],
            'colors.*' => ['filled', 'int', 'exists:colors,id'],

            'sizes' => ['filled', 'array'],
            'sizes.*' => ['filled', 'int', 'exists:sizes,id'],

            'sort' => ['filled', 'in:'.$allowedSortOptionsString],
        ];
    }
}
