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
            'search_query' => ['filled', 'string'],
            'sort' => ['filled', 'string', 'in:' . $allowedSortOptionsString,
            ],
        ];
    }
}
