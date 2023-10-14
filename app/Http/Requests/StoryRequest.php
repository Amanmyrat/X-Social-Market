<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class StoryRequest extends FormRequest
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
    #[ArrayShape(['type' => "string[]", 'image' => "string[]", 'post_id' => "string[]"])]
    public function rules(): array
    {
        return [
            'type' => ['required', 'in:basic,post'],
            'image' => ['required_if:type,basic', 'image'],
            'post_id' => ['required_if:type,post', 'int', 'exists:posts,id'],
        ];
    }
}
