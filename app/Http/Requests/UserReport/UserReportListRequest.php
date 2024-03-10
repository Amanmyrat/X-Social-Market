<?php

namespace App\Http\Requests\UserReport;

use Illuminate\Foundation\Http\FormRequest;

class UserReportListRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $allowedSortOptions = [
            'default',
            'username',
            '-username',
            'is_active',
            '-is_active',
            'created_at',
            '-created_at',
            'reports_count',
            '-reports_count',
        ];
        $allowedSortOptionsString = implode(',', $allowedSortOptions);

        return [
            'limit' => ['filled', 'integer'],
            'search_query' => ['filled', 'string'],
            'sort' => ['filled', 'in:'.$allowedSortOptionsString,
            ],
        ];
    }
}
