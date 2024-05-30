<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckExistenceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'type'  => 'required|string|in:Category,Brand,Location,Color,Size,ReportType',
            'title' => 'required|string'
        ];
    }
}
