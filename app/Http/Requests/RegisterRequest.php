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
        ];
    }
}
