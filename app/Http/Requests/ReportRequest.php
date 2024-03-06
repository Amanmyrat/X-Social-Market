<?php

namespace App\Http\Requests;

use App\Models\ReportType;
use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'report_type_id' => ['required', 'integer', 'exists:'.ReportType::class.',id'],
            'message' => ['filled', 'string'],
        ];
    }
}
