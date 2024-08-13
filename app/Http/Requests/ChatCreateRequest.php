<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChatCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'receiver_user_id' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::notIn([auth()->id()]),
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'receiver_user_id.required' => 'Alyjy ulanyjy ID-si hökmanydyr.',
            'receiver_user_id.integer' => 'Alyjy ulanyjy ID-si diňe sanlardan durmalydyr.',
            'receiver_user_id.exists' => 'Girizilen ulanyjy ID-si bar bolan ID-laryň biri däl.',
            'receiver_user_id.not_in' => 'Alyjy ulanyjy ID-si öz ID-ňiz bolup bilmez.',
        ];
    }

}
