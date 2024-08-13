<?php

namespace App\Http\Requests\Color;

use Illuminate\Foundation\Http\FormRequest;

class ColorCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255'],
            'is_active' => ['filled', 'bool'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Ady ýazmak hökmanydyr.',
            'title.string' => 'Ady dogry görnüşde giriziň.',
            'title.max' => 'Ady iň köp 255 harpdan durmalydyr.',
            'code.required' => 'Kody ýazmak hökmanydyr.',
            'code.string' => 'Kody dogry görnüşde giriziň.',
            'code.max' => 'Kody iň köp 255 harpdan durmalydyr.',
            'is_active.filled' => 'Aktiwlik ýagdaýy girizilen bolmalydyr.',
            'is_active.bool' => 'Aktiwlik ýagdaýy dogry görnüşde bolmalydyr.',
        ];
    }

}
