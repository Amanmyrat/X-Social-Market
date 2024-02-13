<?php

namespace App\Http\Requests\Color;

use Illuminate\Foundation\Http\FormRequest;

class ColorUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['filled', 'string', 'max:255'],
            'code' => ['filled', 'string', 'max:255'],
            'is_active' => ['filled', 'bool'],
        ];
    }
}
