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

    public function messages(): array
    {
        return [
            'title.required' => 'Ady ýazmak hökmanydyr.',
            'title.string' => 'Ady dogry görnüşde giriziň.',
            'title.max' => 'Ady iň köp 255 harpdan durmalydyr.',
            'description.filled' => 'Beýany girizilen bolmalydyr.',
            'description.string' => 'Beýany dogry görnüşde giriziň.',
            'description.max' => 'Beýany iň köp 255 harpdan durmalydyr.',
            'icon.required' => 'Ikon ýazmak hökmanydyr.',
            'icon.image' => 'Ikon surat görnüşinde bolmalydyr.',
            'is_active.filled' => 'Aktiwlik ýagdaýy girizilen bolmalydyr.',
            'is_active.bool' => 'Aktiwlik ýagdaýy dogry görnüşde bolmalydyr.',
            'has_product.required' => 'Önüm barlygyny saýlamak hökmanydyr.',
            'has_product.bool' => 'Önüm barlygy dogry görnüşde bolmalydyr.',
        ];
    }

}
