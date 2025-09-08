<?php

namespace App\Http\Requests\Color;

use Illuminate\Foundation\Http\FormRequest;

class ColorDeleteRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'colors' => 'required|array|min:1',
            'colors.*' => 'int|exists:colors,id',
        ];
    }

    public function messages(): array
    {
        return [
            'colors.required' => 'Reňkleri saýlamak hökmanydyr.',
            'colors.array' => 'Reňkler sanaw görnüşinde bolmalydyr.',
            'colors.min' => 'Azyndan bir reňk saýlanmalydyr.',
            'colors.*.int' => 'Her reňk ID diňe sanlardan durmalydyr.',
            'colors.*.exists' => 'Saýlanan reňk bar bolan ID-laryň biri däl.',
        ];
    }

}
