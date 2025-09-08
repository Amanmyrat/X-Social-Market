<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class PostUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'is_active' => ['required', 'bool'],
            'reason' => ['required_if:is_active,false', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'is_active.required' => 'Aktiwlik ýagdaýy hökmanydyr.',
            'is_active.bool' => 'Aktiwlik ýagdaýy dogry görnüşde bolmalydyr.',
            'reason.required_if' => 'Sebäp hökmanydyr, eger aktiwlik ýagdaýy false bolsa.',
            'reason.string' => 'Sebäp dogry görnüşde giriziň.',
            'reason.max' => 'Sebäp iň köp 255 harpdan durmalydyr.',
        ];
    }

}
