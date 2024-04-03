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
        $rules = [
            'report_type_id' => ['required', 'integer', 'exists:'.ReportType::class.',id'],
            'message' => ['sometimes', 'string'],
        ];

        if ($this->has('report_type_id')) {
            $reportType = ReportType::find($this->input('report_type_id'));

            if ($reportType && $reportType->message_required) {
                $rules['message'] = ['required', 'string'];
            }
        }

        return $rules;
    }
}
