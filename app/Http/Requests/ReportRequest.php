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

    public function messages(): array
    {
        return [
            'report_type_id.required' => 'Hasabat görnüşi ID-si hökmanydyr.',
            'report_type_id.integer' => 'Hasabat görnüşi ID-si diňe sanlardan durmalydyr.',
            'report_type_id.exists' => 'Girizilen hasabat görnüşi ID-si bar bolan ID-laryň biri däl.',
            'message.required' => 'Habar hökmanydyr.',
            'message.string' => 'Habar dogry görnüşde giriziň.',
        ];
    }

}
