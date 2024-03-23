<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'username' => ['filled', 'string', 'min:3', 'alpha_dash', 'unique:'.User::class],
            'email' => ['filled', 'email', 'unique:'.User::class],
            'profile.full_name' => ['filled', 'string', 'min:2'],
            'profile.bio' => ['filled', 'string', 'min:3'],
            'profile.location_id' => ['filled', 'exists:locations,id'],
            'profile.category_id' => ['filled', 'exists:categories,id'],
            'profile.website' => ['filled', 'string', 'min:3'],
            'profile.birthdate' => ['filled', 'date_format:Y-m-d'],
            'profile.gender' => ['filled', 'in:male,female'],
            'profile.payment_available' => ['filled', 'boolean'],
            'profile.private' => ['filled', 'boolean'],
            'profile.profile_image' => ['filled', 'image'],
        ];
    }
}
