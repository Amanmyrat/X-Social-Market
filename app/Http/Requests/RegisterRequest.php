<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\ValidOtpCode;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            /**
             * User phone number.
             *
             * @var string
             *
             * @example 65021734
             */
            'phone' => ['required', 'integer', 'unique:'.User::class],

            /**
             * OTP code sent via SMS.
             *
             * @var integer
             *
             * @example 1234
             */
            'code' => ['required', 'integer', 'between:1000,9999', new ValidOtpCode('phone')],

            /**
             * Device token for push notifications.
             *
             * @var string
             *
             * @example firebase_token_xyz
             */
            'device_token' => ['required', 'string'],

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
            'phone.required' => 'Telefon belgi hökmanydyr.',
            'phone.integer' => 'Telefon belgi diňe sanlardan durmalydyr.',
            'phone.unique' => 'Bu telefon belgi eýýäm bar.',

            'code.required' => 'OTP kod hökmanydyr.',
            'code.integer' => 'OTP kod diňe sanlardan durmalydyr.',
            'code.between' => 'OTP kod 1000 bilen 9999 arasynda bolmalydyr.',

            'device_token.required' => 'Enjam tokeni hökmanydyr.',
            'device_token.string' => 'Enjam tokeni dogry görnüşde giriziň.',

            'referral_code.size' => 'Referral kody 8 simwol bolmaly.',
        ];
    }
}

