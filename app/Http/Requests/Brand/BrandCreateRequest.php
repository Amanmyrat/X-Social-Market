<?php

namespace App\Http\Requests\Brand;

use Illuminate\Foundation\Http\FormRequest;

class BrandCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:simple,clothing'],
            'is_active' => ['filled', 'bool'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Ady ýazmak hökmanydyr.',
            'title.string' => 'Ady dogry görnüşde giriziň.',
            'title.max' => 'Ady iň köp 255 harpdan durmalydyr.',
            'type.required' => 'Görnüşi saýlamak hökmanydyr.',
            'type.in' => 'Görnüşi diňe "simple" ýa-da "clothing" bolup biler.',
            'is_active.filled' => 'Aktiwlik ýagdaýy girizilen bolmalydyr.',
            'is_active.bool' => 'Aktiwlik ýagdaýy dogry görnüşde bolmalydyr.',
        ];
    }

}
