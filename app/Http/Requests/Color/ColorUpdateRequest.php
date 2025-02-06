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
            'title' => ['filled', 'array'],
            'title.tk' => ['required', 'string', 'max:255'],
            'title.ru' => ['nullable', 'string', 'max:255'],
            'title.en' => ['nullable', 'string', 'max:255'],

            'code' => ['filled', 'string', 'max:255'],
            'is_active' => ['filled', 'bool'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.filled' => 'Kody girizilen bolmalydyr.',
            'code.string' => 'Kody dogry görnüşde giriziň.',
            'code.max' => 'Kody iň köp 255 harpdan durmalydyr.',
            'is_active.filled' => 'Aktiwlik ýagdaýy girizilen bolmalydyr.',
            'is_active.bool' => 'Aktiwlik ýagdaýy dogry görnüşde bolmalydyr.',
        ];
    }

}
