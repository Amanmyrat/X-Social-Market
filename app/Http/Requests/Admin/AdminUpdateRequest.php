<?php

namespace App\Http\Requests\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Http\FormRequest;

class AdminUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'phone' => ['filled', 'integer', 'unique:'.Admin::class],
            'name' => ['filled', 'string', 'min:3'],
            'surname' => ['filled', 'string', 'min:3'],
            'profile_image' => ['filled', 'image'],
            'is_active' => ['filled', 'bool'],
            'role' => ['filled', 'string', 'exists:roles,name'],
            'permissions' => ['filled', 'array'],
            'permissions.*' => ['filled', 'string', 'exists:permissions,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.filled' => 'Telefon belgi girizilen bolmalydyr.',
            'phone.integer' => 'Telefon belgi diňe sanlardan durmalydyr.',
            'phone.unique' => 'Bu telefon belgi eýýäm bar.',
            'name.filled' => 'Ady girizilen bolmalydyr.',
            'name.string' => 'Ady dogry görnüşde giriziň.',
            'name.min' => 'Ady azyndan 3 harpdan ybarat bolmalydyr.',
            'surname.filled' => 'Familiýa girizilen bolmalydyr.',
            'surname.string' => 'Familiýany dogry görnüşde giriziň.',
            'surname.min' => 'Familiýa azyndan 3 harpdan ybarat bolmalydyr.',
            'profile_image.filled' => 'Profil suraty girizilen bolmalydyr.',
            'profile_image.image' => 'Profil suraty şekil bolmalydyr.',
            'is_active.filled' => 'Aktiwlik ýagdaýy girizilen bolmalydyr.',
            'is_active.bool' => 'Aktiwlik ýagdaýy dogry görnüşde bolmalydyr.',
            'role.filled' => 'Roly girizilen bolmalydyr.',
            'role.string' => 'Roly dogry görnüşde giriziň.',
            'role.exists' => 'Saýlanan rol bar bolan rolyň biri däl.',
            'permissions.filled' => 'Rugsatnamalar girizilen bolmalydyr.',
            'permissions.array' => 'Rugsatnamalar sanaw görnüşinde bolmalydyr.',
            'permissions.*.filled' => 'Her bir rugsatnama girizilen bolmalydyr.',
            'permissions.*.string' => 'Her bir rugsatnama dogry görnüşde bolmalydyr.',
            'permissions.*.exists' => 'Her bir rugsatnama bar bolan rugsatnamalaryň biri däl.',
        ];
    }

}
