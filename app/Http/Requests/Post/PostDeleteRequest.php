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

    public function messages(): array
    {
        return [
            'posts.required' => 'Postlary saýlamak hökmanydyr.',
            'posts.array' => 'Postlar sanaw görnüşinde bolmalydyr.',
            'posts.min' => 'Azyndan bir post saýlanmalydyr.',
            'posts.*.int' => 'Her post ID diňe sanlardan durmalydyr.',
            'posts.*.exists' => 'Saýlanan post bar bolan ID-laryň biri däl.',
        ];
    }

}
