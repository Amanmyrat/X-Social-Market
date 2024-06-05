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
}
