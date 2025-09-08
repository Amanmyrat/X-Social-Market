<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrivacyPolicyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'content_en' => 'required|string',
            'content_ru' => 'required|string',
            'content_tk' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'content_en.required' => 'Iňlis dilindäki mazmun hökmanydyr.',
            'content_en.string' => 'Iňlis dilindäki mazmun dogry görnüşde giriziň.',
            'content_ru.required' => 'Rus dilindäki mazmun hökmanydyr.',
            'content_ru.string' => 'Rus dilindäki mazmun dogry görnüşde giriziň.',
            'content_tk.required' => 'Türkmen dilindäki mazmun hökmanydyr.',
            'content_tk.string' => 'Türkmen dilindäki mazmun dogry görnüşde giriziň.',
        ];
    }

}
