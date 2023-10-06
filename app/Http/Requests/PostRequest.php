<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'caption' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'can_comment' => ['required', 'boolean'],
            'media_type' => ['required', 'in:image,video'],
            'images' => ['required_if:media_type,image', 'max:8'],
            'videos' => ['required_if:media_type,video', 'max:5'],
        ];
    }
}
