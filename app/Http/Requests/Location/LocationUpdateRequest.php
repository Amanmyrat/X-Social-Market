<?php

namespace App\Http\Requests\Location;

use Illuminate\Foundation\Http\FormRequest;

class LocationUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['filled', 'string', 'max:255'],
            'is_active' => ['filled', 'bool'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.filled' => 'Ady girizilen bolmalydyr.',
            'title.string' => 'Ady dogry görnüşde giriziň.',
            'title.max' => 'Ady iň köp 255 harpdan durmalydyr.',
            'is_active.filled' => 'Aktiwlik ýagdaýy girizilen bolmalydyr.',
            'is_active.bool' => 'Aktiwlik ýagdaýy dogry görnüşde bolmalydyr.',
        ];
    }

}
