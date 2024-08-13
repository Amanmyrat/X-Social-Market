<?php

namespace App\Http\Requests\Size;

use Illuminate\Foundation\Http\FormRequest;

class SizeDeleteRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'sizes' => 'required|array|min:1',
            'sizes.*' => 'int|exists:sizes,id',
        ];
    }

    public function messages(): array
    {
        return [
            'sizes.required' => 'Ölçegleri saýlamak hökmanydyr.',
            'sizes.array' => 'Ölçegler sanaw görnüşinde bolmalydyr.',
            'sizes.min' => 'Azyndan bir ölçeg saýlanmalydyr.',
            'sizes.*.int' => 'Her ölçeg ID-si diňe sanlardan durmalydyr.',
            'sizes.*.exists' => 'Saýlanan ölçeg bar bolan ID-laryň biri däl.',
        ];
    }

}
