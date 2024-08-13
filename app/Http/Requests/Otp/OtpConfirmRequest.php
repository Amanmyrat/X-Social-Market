<?php

namespace App\Http\Requests\Otp;

use Illuminate\Foundation\Http\FormRequest;

class OtpConfirmRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'integer', 'between:1000,9999'],
            'phone' => ['required', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kod hökmanydyr.',
            'code.integer' => 'Kod diňe sanlardan durmalydyr.',
            'code.between' => 'Kod 1000 bilen 9999 arasynda bolmalydyr.',
            'phone.required' => 'Telefon belgi hökmanydyr.',
            'phone.integer' => 'Telefon belgi diňe sanlardan durmalydyr.',
        ];
    }

}
