<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminDeleteRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'admins' => 'required|array|min:1',
            'admins.*' => 'int|exists:admins,id',
        ];
    }

    /**
     * Get the custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'admins.required' => 'Adminleri saýlamak hökmanydyr.',
            'admins.array' => 'Adminler sanaw görnüşinde bolmalydyr.',
            'admins.min' => 'Azyndan bir admin saýlanmalydyr.',
            'admins.*.int' => 'Her admin ID diňe sanlardan durmalydyr.',
            'admins.*.exists' => 'Saýlanan admin bar bolan ID-laryň biri däl.',
        ];
    }
}
