<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            /**
             * User login(phone or username).
             *
             * @var string
             *
             * @example 65021734
             */
            'phone' => ['required', 'integer', 'unique:'.User::class],
            'device_token' => ['required', 'string'],

            /**
             * Admin password.
             *
             * @var string
             *
             * @example 12345678
             */
            'password' => ['required', 'confirmed', Password::defaults()],
            
            /**
             * Referral code (optional).
             *
             * @var string
             *
             * @example AB12CD34
             */
            'referral_code' => ['nullable', 'string', 'size:8'],
        ];
    }

    /**
     * Get the custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'phone.exists' => 'Bu telefon belgisi ulanylýar.',
            'phone.required' => 'Telefon belgi hökmanydyr.',
            'phone.integer' => 'Telefon belgi diňe sanlardan durmalydyr.',
            'phone.unique' => 'Bu telefon belgi eýýäm bar.',
            'device_token.required' => 'Enjam tokeni hökmanydyr.',
            'device_token.string' => 'Enjam tokeni dogry görnüşde giriziň.',
            'password.required' => 'Parol hökmanydyr.',
            'password.confirmed' => 'Parollar gabat gelmeli.',
            'referral_code.size' => 'Referral kody 8 simwol bolmaly.',
        ];
    }

}
