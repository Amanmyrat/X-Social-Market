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
            'sort' => ['filled', 'string', 'in:'.$allowedSortOptionsString,
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
