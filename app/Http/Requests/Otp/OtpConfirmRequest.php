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
}
