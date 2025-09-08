<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminListRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $allowedSortOptions = [
            'default',
            'name',
            '-name',
            'email',
            '-email',
            'is_active',
            '-is_active',
            'last_activity',
            '-last_activity',
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
