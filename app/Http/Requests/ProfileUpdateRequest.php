<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'full_name' => ['filled', 'string', 'min:2'],
            'bio' => ['filled', 'string', 'min:3'],
            'location_id' => ['filled', 'exists:locations,id'],
            'category_id' => ['filled', 'exists:categories,id'],
            'website' => ['filled', 'string', 'min:3'],
            'birthdate' => ['filled', 'date_format:Y-m-d'],
            'gender' => ['filled', 'in:male,female'],
            'payment_available' => ['filled', 'boolean'],
            'private' => ['filled', 'boolean'],
            'profile_image' => ['filled', 'image'],
        ];
    }
}
