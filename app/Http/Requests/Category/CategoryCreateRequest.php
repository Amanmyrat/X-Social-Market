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
            'title' => ['required', 'array'],
            'title.tk' => ['required', 'string', 'max:255'],
            'title.ru' => ['nullable', 'string', 'max:255'],
            'title.en' => ['nullable', 'string', 'max:255'],

            'description' => ['nullable', 'array'],
            'description.tk' => ['nullable', 'string', 'max:255'],
            'description.ru' => ['nullable', 'string', 'max:255'],
            'description.en' => ['nullable', 'string', 'max:255'],

            'icon' => ['required', 'image'],
            'is_active' => ['filled', 'boolean'],
            'has_product' => ['required', 'boolean'],
        ];

    }

    public function messages(): array
    {
        return [
            'icon.required' => 'Ikon ýazmak hökmanydyr.',
            'icon.image' => 'Ikon surat görnüşinde bolmalydyr.',
            'is_active.filled' => 'Aktiwlik ýagdaýy girizilen bolmalydyr.',
            'is_active.bool' => 'Aktiwlik ýagdaýy dogry görnüşde bolmalydyr.',
            'has_product.required' => 'Önüm barlygyny saýlamak hökmanydyr.',
            'has_product.bool' => 'Önüm barlygy dogry görnüşde bolmalydyr.',
        ];
    }

}
