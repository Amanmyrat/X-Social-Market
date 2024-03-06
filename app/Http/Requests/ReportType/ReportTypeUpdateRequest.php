<?php

namespace App\Http\Requests\ReportType;

use Illuminate\Foundation\Http\FormRequest;

class ReportTypeUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['filled', 'string', 'max:255'],
            'is_active' => ['filled', 'bool'],
        ];
    }
}
