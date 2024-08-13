<?php

namespace App\Http\Requests\ReportType;

use Illuminate\Foundation\Http\FormRequest;

class ReportTypeListRequest extends FormRequest
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
            'created_at',
            '-created_at',
            'story_reports_count',
            '-story_reports_count',
            'post_reports_count',
            '-post_reports_count',
            'user_reports_count',
            '-user_reports_count',
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
