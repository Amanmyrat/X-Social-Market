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
            'type' => 'required|in:message,share_story,share_post,post_discussion,media,file',
            'body' => 'required_if:type,message|string',
            'story_id' => 'required_if:type,share_story|exists:stories,id',
            'post_id' => 'required_if:type,share_post,post_discussion|exists:posts,id',
            'file' => 'required_if:type,file|file',
            'media_type' => ['required_if:type,media', 'in:image,video'],
            'images' => ['required_if:media_type,image', 'max:5'],
            'videos' => ['required_if:media_type,video', 'max:2'],
        ];
    }

    public function messages(): array
    {
        return [
            'chat_id.required' => 'Söhbetdeşlik ID-si hökmanydyr.',
            'chat_id.integer' => 'Söhbetdeşlik ID-si diňe sanlardan durmalydyr.',
            'chat_id.exists' => 'Söhbetdeşlik nädogrydyr ýa-da girizilen ulanyjylara degişli däl.',
            'receiver_user_id.required' => 'Alyjy ulanyjy ID-si hökmanydyr.',
            'receiver_user_id.integer' => 'Alyjy ulanyjy ID-si diňe sanlardan durmalydyr.',
            'receiver_user_id.exists' => 'Girizilen ulanyjy ID-si bar bolan ID-laryň biri däl.',
            'receiver_user_id.not_in' => 'Alyjy ulanyjy ID-si öz ID-ňiz bolup bilmez.',
            'type.required' => 'Habar görnüşi hökmanydyr.',
            'type.in' => 'Habar görnüşi diňe "message", "share_story", "share_post", "post_discussion", "media", "file" bolup biler.',
            'body.required_if' => 'Habar teksti hökmanydyr.',
            'body.string' => 'Habar teksti dogry görnüşde giriziň.',
            'story_id.required_if' => 'Hekaýa paýlaşmak üçin hekaýa ID-si hökmanydyr.',
            'story_id.exists' => 'Girizilen hekaýa ID-si bar bolan ID-laryň biri däl.',
            'post_id.required_if' => 'Post paýlaşmak ýa-da post müzakirasy üçin post ID-si hökmanydyr.',
            'post_id.exists' => 'Girizilen post ID-si bar bolan ID-laryň biri däl.',
            'file.required_if' => 'Faýl ibermek üçin faýl hökmanydyr.',
            'file.file' => 'Faýl dogry formatda bolmalydyr.',
            'media_type.required_if' => 'Media görnüşi hökmanydyr.',
            'media_type.in' => 'Media görnüşi diňe "image" ýa-da "video" bolup biler.',
            'images.required_if' => 'Suratlar girizilen bolmalydyr.',
            'images.max' => 'Suratlaryň sany iň köp 5 bolmalydyr.',
            'videos.required_if' => 'Wideolar girizilen bolmalydyr.',
            'videos.max' => 'Wideolaryň sany iň köp 2 bolmalydyr.',
        ];
    }

}
