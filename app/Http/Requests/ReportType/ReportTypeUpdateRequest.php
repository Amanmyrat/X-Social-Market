<?php

namespace App\Http\Requests\ReportType;

use Illuminate\Foundation\Http\FormRequest;

class ReportTypeUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['filled', 'array'],
            'title.tk' => ['required', 'string', 'max:255'],
            'title.ru' => ['nullable', 'string', 'max:255'],
            'title.en' => ['nullable', 'string', 'max:255'],

            'is_active' => ['filled', 'bool'],
            'message_required' => ['filled', 'bool'],
        ];
    }

    public function messages(): array
    {
        return [

            'is_active.filled' => 'Aktiwlik ýagdaýy girizilen bolmalydyr.',
            'is_active.bool' => 'Aktiwlik ýagdaýy dogry görnüşde bolmalydyr.',
            'message_required.filled' => 'Habar hökmanylygy girizilen bolmalydyr.',
            'message_required.bool' => 'Habar hökmanylygy dogry görnüşde bolmalydyr.',
        ];
    }

}
