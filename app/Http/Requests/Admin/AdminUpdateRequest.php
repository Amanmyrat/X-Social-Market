<?php

namespace App\Http\Requests\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Http\FormRequest;

class AdminUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'phone' => ['filled', 'integer', 'unique:'.Admin::class],
            'name' => ['filled', 'string', 'min:3'],
            'surname' => ['filled', 'string', 'min:3'],
            'profile_image' => ['filled', 'image'],
            'is_active' => ['filled', 'bool'],
            'role' => ['filled', 'string', 'exists:roles,name'],
            'permissions' => ['filled', 'array'],
            'permissions.*' => ['filled', 'string', 'exists:permissions,name'],
        ];
    }
}
