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
            'tags' => ['array'],
            'tags.*.user_id' => ['nullable', 'exists:users,id'],
            'tags.*.name' => ['nullable', 'string'],
            'tags.*.dx' => ['required', 'numeric'],
            'tags.*.dy' => ['required', 'numeric'],
            'tags.*.text_options' => ['nullable', 'json'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Görnüşi ýazmak hökmanydyr.',
            'type.in' => 'Görnüşi diňe "basic" ýa-da "post" bolup biler.',
            'image.required_if' => 'Görnüşi "basic" bolanda surat hökmanydyr.',
            'image.image' => 'Girizilen faýl surat bolmalydyr.',
            'post_id.required_if' => 'Görnüşi "post" bolanda post ID-si hökmanydyr.',
            'post_id.int' => 'Post ID-si diňe sanlardan durmalydyr.',
            'post_id.exists' => 'Girizilen post ID-si bar bolan ID-laryň biri däl.',
            'tags.array' => 'Tags should be an array.',
            'tags.*.dx.required' => 'The X position of the tag is required.',
            'tags.*.dy.required' => 'The Y position of the tag is required.',
            'tags.*.user_id.exists' => 'The tagged user does not exist.',
            'tags.*.text_options.json' => 'The text options must be a valid JSON format.',
        ];
    }

}
