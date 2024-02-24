<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FollowerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'following_id' => [
                'required',
                'integer',
                Rule::exists(User::class, 'id'),
                function ($attribute, $value, $fail) {
                    /** @var User $user */
                    $user = Auth::user();

                    if ($value == $user->id) {
                        $fail($attribute.' cannot be the same as your user ID.');
                    }
                },
            ],
        ];
    }
}
