<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'in:basic,post'],
            'image' => ['required_if:type,basic', 'image'],
            'post_id' => ['required_if:type,post', 'int', 'exists:posts,id'],
        ];
    }
}
