<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MessageSendRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'chat_id' => [
                'required',
                'integer',
                Rule::exists('chats', 'id')->where(function ($query) {
                    $userIds = [auth()->id(), (int) $this->input('receiver_user_id')];
                    $query->whereIn('sender_user_id', $userIds)
                        ->whereIn('receiver_user_id', $userIds);
                }),
            ],
            'receiver_user_id' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::notIn([auth()->id()]),
            ],
            'type' => 'required|in:message,share_story,share_post,media,file',
            'body' => 'required_if:type,message|string',
            'story_id' => 'required_if:type,share_story|exists:stories,id',
            'post_id' => 'required_if:type,share_post|exists:posts,id',
            'file' => 'required_if:type,file|file',
            'media_type' => ['required_if:type,media', 'in:image,video'],
            'images' => ['required_if:media_type,image', 'max:5'],
            'videos' => ['required_if:media_type,video', 'max:2'],
        ];
    }
}
