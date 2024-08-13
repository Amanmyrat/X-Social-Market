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

    /**
     * Get the custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'limit.filled' => 'Çäk girizilen bolmalydyr.',
            'limit.integer' => 'Çäk diňe sanlardan durmalydyr.',
            'sort.filled' => 'Tertipleme saýlanan bolmalydyr.',
            'sort.in' => 'Tertipleme üçin dogry birikdirilen opsiýalary saýlaň: default, name, -name, email, -email, is_active, -is_active, last_activity, -last_activity.',
        ];
    }
}
