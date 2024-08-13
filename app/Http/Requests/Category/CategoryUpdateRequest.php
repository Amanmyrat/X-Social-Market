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
            'title' => ['filled', 'string', 'max:255'],
            'description' => ['filled', 'string', 'max:255'],
            'icon' => ['filled', 'image'],
            'is_active' => ['filled', 'bool'],
            'has_product' => ['filled', 'bool'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.filled' => 'Ady girizilen bolmalydyr.',
            'title.string' => 'Ady dogry görnüşde giriziň.',
            'title.max' => 'Ady iň köp 255 harpdan durmalydyr.',
            'description.filled' => 'Beýany girizilen bolmalydyr.',
            'description.string' => 'Beýany dogry görnüşde giriziň.',
            'description.max' => 'Beýany iň köp 255 harpdan durmalydyr.',
            'icon.filled' => 'Ikon girizilen bolmalydyr.',
            'icon.image' => 'Ikon surat görnüşinde bolmalydyr.',
            'is_active.filled' => 'Aktiwlik ýagdaýy girizilen bolmalydyr.',
            'is_active.bool' => 'Aktiwlik ýagdaýy dogry görnüşde bolmalydyr.',
            'has_product.filled' => 'Önüm barlygy girizilen bolmalydyr.',
            'has_product.bool' => 'Önüm barlygy dogry görnüşde bolmalydyr.',
        ];
    }

}
