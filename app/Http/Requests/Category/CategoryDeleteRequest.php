<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class CategoryDeleteRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'categories' => 'required|array|min:1',
            'categories.*' => 'int|exists:categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            'categories.required' => 'Kategorileri saýlamak hökmanydyr.',
            'categories.array' => 'Kategoriler sanaw görnüşinde bolmalydyr.',
            'categories.min' => 'Azyndan bir kategoriýa saýlanmalydyr.',
            'categories.*.int' => 'Her kategoriýa ID diňe sanlardan durmalydyr.',
            'categories.*.exists' => 'Saýlanan kategoriýa bar bolan ID-laryň biri däl.',
        ];
    }

}
