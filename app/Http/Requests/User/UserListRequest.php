<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserListRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $allowedSortOptions = [
            'default',
            'username',
            '-username',
            'is_active',
            '-is_active',
            'created_at',
            '-created_at',
        ];
        $allowedSortOptionsString = implode(',', $allowedSortOptions);

        return [
            'limit' => ['filled', 'integer'],
            'sort' => ['filled', 'in:'.$allowedSortOptionsString],
        ];
    }
}
