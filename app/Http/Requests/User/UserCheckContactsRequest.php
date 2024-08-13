<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UserCheckContactsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [

            /**
             * User contacts.
             *
             * @var array
             *
             * @example [65000000,65000001,65000002]
             */
            'contacts' => 'required|array|min:1',
            'contacts.*' => 'required|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'contacts.required' => 'Kontaktlary saýlamak hökmanydyr.',
            'contacts.array' => 'Kontaktlar sanaw görnüşinde bolmalydyr.',
            'contacts.min' => 'Azyndan bir kontakt saýlanmalydyr.',
            'contacts.*.required' => 'Her bir kontakt hökmanydyr.',
            'contacts.*.numeric' => 'Her bir kontakt diňe sanlardan durmalydyr.',
        ];
    }

}
