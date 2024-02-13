<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class CategoryCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['filled', 'string', 'max:255'],
            'icon' => ['required', 'image'],
            'is_active' => ['filled', 'bool'],
            'has_product' => ['required', 'bool'],
        ];
    }
}
