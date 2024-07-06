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
}
