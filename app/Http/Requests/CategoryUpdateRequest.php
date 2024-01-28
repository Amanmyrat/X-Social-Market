<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryUpdateRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => ['filled', 'string', 'max:255'],
            'description' => ['filled', 'string', 'max:255'],
            'icon' => ['filled', 'image'],
            'is_active' => ['filled', 'bool'],
            'has_product' => ['filled', 'bool'],
        ];
    }
}
