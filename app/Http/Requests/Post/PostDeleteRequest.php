<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class PostDeleteRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'posts' => 'required|array|min:1',
            'posts.*' => 'int|exists:posts,id',
        ];
    }
}
