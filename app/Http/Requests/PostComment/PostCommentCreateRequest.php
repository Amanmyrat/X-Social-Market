<?php

namespace App\Http\Requests\PostComment;

use App\Models\PostComment;
use Illuminate\Foundation\Http\FormRequest;

class PostCommentCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'comment' => ['required', 'string', 'max:255'],
            'parent_id' => ['sometimes', 'int'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $parentCommentId = $this->input('parent_id');
            $post = $this->route('post');

            if (!is_null($parentCommentId) && $parentCommentId !== 0) {
                $parentComment = PostComment::find($parentCommentId, ['post_id']);

                if (!$parentComment) {
                    $validator->errors()->add('parent_id', 'The selected parent_id is invalid.');
                } elseif ($parentComment->post_id != $post->id) {
                    $validator->errors()->add('parent_id', 'The parent_id does not belong to the provided post.');
                }
            }
        });
    }

}
