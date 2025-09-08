<?php

namespace App\Http\Requests\Admin;

use App\Enum\AdminRole;
use App\Models\Admin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AdminCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'phone' => ['required', 'integer', 'unique:'.Admin::class],
            'name' => ['required', 'string', 'min:3'],
            'surname' => ['required', 'string', 'min:3'],
            'email' => ['required', 'email', 'unique:'.Admin::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            'profile_image' => ['filled', 'image'],
            'is_active' => ['filled', 'bool'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'permissions' => ['required_if:role,'.AdminRole::Admin->value, 'array'],
            'permissions.*' => ['required_if:role,'.AdminRole::Admin->value, 'string', 'exists:permissions,name'],
        ];
    }

    /**
     * Get the custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'phone.required' => 'Telefon belgi hökmanydyr.',
            'phone.integer' => 'Telefon belgi diňe sanlardan durmalydyr.',
            'phone.unique' => 'Bu telefon belgi eýýäm bar.',
            'name.required' => 'Ady ýazmak hökmanydyr.',
            'name.string' => 'Ady dogry görnüşde giriziň.',
            'name.min' => 'Ady azyndan 3 harpdan ybarat bolmalydyr.',
            'surname.required' => 'Familiýa ýazmak hökmanydyr.',
            'surname.string' => 'Familiýany dogry görnüşde giriziň.',
            'surname.min' => 'Familiýa azyndan 3 harpdan ybarat bolmalydyr.',
            'email.required' => 'Email ýazmak hökmanydyr.',
            'email.email' => 'Dogry email adresini giriziň.',
            'email.unique' => 'Bu email adres eýýäm bar.',
            'password.required' => 'Parol ýazmak hökmanydyr.',
            'password.confirmed' => 'Paroly tassyklaň.',
            'profile_image.filled' => 'Profil suraty girizilen bolmalydyr.',
            'profile_image.image' => 'Profil suraty şekil bolmalydyr.',
            'is_active.filled' => 'Aktiwlik ýagdaýy girizilen bolmalydyr.',
            'is_active.bool' => 'Aktiwlik ýagdaýy bolmaly.',
            'role.required' => 'Roly saýlamak hökmanydyr.',
            'role.string' => 'Roly dogry görnüşde giriziň.',
            'role.exists' => 'Saýlanan rol bar bolan rolyň biri däl.',
            'permissions.required_if' => 'Admin roly üçin rugsatnamalar hökmanydyr.',
            'permissions.array' => 'Rugsatnamalar sanaw görnüşinde bolmalydyr.',
            'permissions.*.required_if' => 'Admin roly üçin rugsatnamalar her biri hökmanydyr.',
            'permissions.*.string' => 'Her bir rugsatnama dogry görnüşde bolmalydyr.',
            'permissions.*.exists' => 'Her bir rugsatnama bar bolan rugsatnamalaryň biri däl.',
        ];
    }
}
