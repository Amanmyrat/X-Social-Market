<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class CategoryUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['filled', 'array'],
            'title.tk' => ['required', 'string', 'max:255'],
            'title.ru' => ['nullable', 'string', 'max:255'],
            'title.en' => ['nullable', 'string', 'max:255'],

            'description' => ['nullable', 'array'],
            'description.tk' => ['nullable', 'string', 'max:255'],
            'description.ru' => ['nullable', 'string', 'max:255'],
            'description.en' => ['nullable', 'string', 'max:255'],

            'icon' => ['filled', 'image'],
            'is_active' => ['filled', 'bool'],
            'has_product' => ['filled', 'bool'],
        ];
    }

    public function messages(): array
    {
        return [
            'icon.filled' => 'Ikon girizilen bolmalydyr.',
            'icon.image' => 'Ikon surat görnüşinde bolmalydyr.',
            'is_active.filled' => 'Aktiwlik ýagdaýy girizilen bolmalydyr.',
            'is_active.bool' => 'Aktiwlik ýagdaýy dogry görnüşde bolmalydyr.',
            'has_product.filled' => 'Önüm barlygy girizilen bolmalydyr.',
            'has_product.bool' => 'Önüm barlygy dogry görnüşde bolmalydyr.',
        ];
    }

}
