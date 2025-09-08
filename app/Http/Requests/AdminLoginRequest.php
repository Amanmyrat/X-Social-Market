<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminLoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            /**
             * Admin email.
             *
             * @var string
             *
             * @example super_admin@gmail.com
             */
            'email' => 'required|email',

            /**
             * Admin password.
             *
             * @var string
             *
             * @example 12345678
             */
            'password' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email adresi hökmanydyr.',
            'email.email' => 'Dogry email adresini giriziň.',
            'password.required' => 'Parol hökmanydyr.',
        ];
    }

}
