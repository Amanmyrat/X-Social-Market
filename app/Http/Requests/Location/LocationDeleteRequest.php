<?php

namespace App\Http\Requests\Location;

use Illuminate\Foundation\Http\FormRequest;

class LocationDeleteRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'locations' => 'required|array|min:1',
            'locations.*' => 'int|exists:locations,id',
        ];
    }

    public function messages(): array
    {
        return [
            'locations.required' => 'Ýerler saýlamak hökmanydyr.',
            'locations.array' => 'Ýerler sanaw görnüşinde bolmalydyr.',
            'locations.min' => 'Azyndan bir ýer saýlanmalydyr.',
            'locations.*.int' => 'Her ýer ID diňe sanlardan durmalydyr.',
            'locations.*.exists' => 'Saýlanan ýer bar bolan ID-laryň biri däl.',
        ];
    }

}
