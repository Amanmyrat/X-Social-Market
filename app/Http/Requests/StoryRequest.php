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
        ];
    }

}
