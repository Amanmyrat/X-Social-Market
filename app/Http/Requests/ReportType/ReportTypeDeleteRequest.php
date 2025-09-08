<?php

namespace App\Http\Requests\ReportType;

use Illuminate\Foundation\Http\FormRequest;

class ReportTypeDeleteRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'types' => 'required|array|min:1',
            'types.*' => 'int|exists:report_types,id',
        ];
    }

    public function messages(): array
    {
        return [
            'types.required' => 'Týplary saýlamak hökmanydyr.',
            'types.array' => 'Týplar sanaw görnüşinde bolmalydyr.',
            'types.min' => 'Azyndan bir typ saýlanmalydyr.',
            'types.*.int' => 'Her typ ID diňe sanlardan durmalydyr.',
            'types.*.exists' => 'Saýlanan typ bar bolan ID-laryň biri däl.',
        ];
    }

}
