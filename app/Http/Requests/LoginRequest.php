<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\ValidOtpCode;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            /**
             * User login (phone or username).
             *
             * @var string
             *
             * @example 65021734 or ulanyjy_12345678
             */
            'login' => ['required', 'string'],

            /**
             * OTP code sent via SMS.
             *
             * @var integer
             *
             * @example 1234
             */
            'code' => ['required', 'integer', 'between:1000,9999', new ValidOtpCode('phone_from_login')],

            /**
             * Device token for push notifications.
             *
             * @var string
             *
             * @example firebase_token_xyz
             */
            'device_token' => ['nullable', 'string'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Get phone from login field (could be phone or username)
        $login = $this->input('login');
        $field = filter_var($login, FILTER_VALIDATE_INT) ? 'phone' : 'username';

        // Find user by phone or username
        $user = User::where($field, $login)->first();

        if ($user) {
            // Store actual phone in a temp field for ValidOtpCode rule
            $this->merge(['phone_from_login' => $user->phone]);
        }
    }

    /**
     * Get the custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'login.required' => 'Telefon ýa-da ulanyjy ady hökmanydyr.',
            'login.string' => 'Telefon ýa-da ulanyjy ady dogry görnüşde giriziň.',

            'code.required' => 'OTP kod hökmanydyr.',
            'code.integer' => 'OTP kod diňe sanlardan durmalydyr.',
            'code.between' => 'OTP kod 1000 bilen 9999 arasynda bolmalydyr.',

            'device_token.string' => 'Enjam tokeni dogry görnüşde giriziň.',
        ];
    }
}
