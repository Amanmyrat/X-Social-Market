<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckExistenceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'type'  => 'required|string|in:Category,Brand,Location,Color,Size,ReportType',
            'title' => 'required|string'
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Görnüşi ýazmak hökmanydyr.',
            'type.string' => 'Görnüşi dogry görnüşde giriziň.',
            'type.in' => 'Görnüşi diňe "Category", "Brand", "Location", "Color", "Size", "ReportType" bolup biler.',
            'title.required' => 'Ady ýazmak hökmanydyr.',
            'title.string' => 'Ady dogry görnüşde giriziň.',
        ];
    }

}
