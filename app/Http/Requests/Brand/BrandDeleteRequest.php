<?php

namespace App\Http\Requests\Brand;

use Illuminate\Foundation\Http\FormRequest;

class BrandDeleteRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'brands' => 'required|array|min:1',
            'brands.*' => 'int|exists:brands,id',
        ];
    }

    public function messages(): array
    {
        return [
            'brands.required' => 'Markalary saýlamak hökmanydyr.',
            'brands.array' => 'Markalar sanaw görnüşinde bolmalydyr.',
            'brands.min' => 'Azyndan bir marka saýlanmalydyr.',
            'brands.*.int' => 'Her marka ID diňe sanlardan durmalydyr.',
            'brands.*.exists' => 'Saýlanan marka bar bolan ID-laryň biri däl.',
        ];
    }

}
