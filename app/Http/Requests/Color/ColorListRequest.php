<?php

namespace App\Http\Requests\Color;

use Illuminate\Foundation\Http\FormRequest;

class ColorListRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'limit' => ['filled', 'integer'],
            'search_query' => ['filled', 'string'],
            'sort' => ['filled', 'string', 'in:default,
                        title,-title,
                        is_active,-is_active,
                        created_at,-created_at'
            ],
        ];
    }
}
