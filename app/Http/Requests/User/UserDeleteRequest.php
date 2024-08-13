<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserDeleteRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'users' => 'required|array|min:1',
            'users.*' => 'int|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'users.required' => 'Ulanyjylary saýlamak hökmanydyr.',
            'users.array' => 'Ulanyjylar sanaw görnüşinde bolmalydyr.',
            'users.min' => 'Azyndan bir ulanyjy saýlanmalydyr.',
            'users.*.int' => 'Her ulanyjy ID-si diňe sanlardan durmalydyr.',
            'users.*.exists' => 'Saýlanan ulanyjy bar bolan ID-laryň biri däl.',
        ];
    }

}
