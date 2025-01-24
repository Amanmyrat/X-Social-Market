<?php

namespace App\Http\Requests;

use App\Rules\ProductDetailsValidation;
use Illuminate\Foundation\Http\FormRequest;

class PostUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'caption' => ['sometimes', 'string'],
            'description' => ['nullable', 'string'],
            'can_comment' => ['sometimes', 'boolean'],
            'medias' => ['sometimes', 'array', 'max:8'],
            'medias.*' => ['sometimes', 'file', 'mimes:jpg,jpeg,png,mp4,webp,gif,mpeg4,mov,heic,heif'],

            'tags' => ['array'],
            'tags.*.tag_post_id' => ['nullable', 'exists:posts,id'],
            'tags.*.dx' => ['required', 'numeric'],
            'tags.*.dy' => ['required', 'numeric'],
            'tags.*.text_options' => ['nullable', 'json'],
        ];
    }

    public function messages(): array
    {
        return [
            'caption.string' => 'Mazmun dogry görnüşde giriziň.',
            'caption.max' => 'Mazmun iň köp 255 harpdan durmalydyr.',
            'description.string' => 'Beýany dogry görnüşde giriziň.',
            'description.max' => 'Beýan iň köp 255 harpdan durmalydyr.',
            'can_comment.boolean' => 'Teswir ýazylyp bilinýänligi dogry görnüşde bolmalydyr.',
            'medias.array' => 'Media faýllary sanaw görnüşinde bolmalydyr.',
            'medias.max' => 'Iň köp 8 media faýl goýulyp bilner.',
            'medias.*.file' => 'Her bir media faýl faýl görnüşinde bolmalydyr.',
            'medias.*.mimes' => 'Media faýllar şu formatlarda bolmalydyr: jpg, jpeg, png, mp4, webp, gif, mpeg4, mov, heic, heif',
        ];
    }

}
