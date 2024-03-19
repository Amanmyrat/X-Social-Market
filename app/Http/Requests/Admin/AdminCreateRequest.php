<?php

namespace App\Http\Requests\Admin;

use App\Enum\AdminRole;
use App\Models\Admin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AdminCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'phone' => ['required', 'integer', 'unique:'.Admin::class],
            'name' => ['required', 'string', 'min:3'],
            'surname' => ['required', 'string', 'min:3'],
            'email' => ['required', 'email', 'unique:'.Admin::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            'profile_image' => ['filled', 'image'],
            'is_active' => ['filled', 'bool'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'permissions' => ['required_if:role,'.AdminRole::Admin->value, 'array'],
            'permissions.*' => ['required_if:role,'.AdminRole::Admin->value, 'string', 'exists:permissions,name'],
        ];
    }
}
