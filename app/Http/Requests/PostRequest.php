<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    #[ArrayShape(['caption' => "string[]", 'location' => "string[]", 'can_comment' => "string[]", 'media_type' => "string[]", 'images' => "string[]", 'videos' => "string[]"])]
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
