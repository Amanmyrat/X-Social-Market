<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'phone' => ['filled', 'integer', 'unique:'.User::class],
            'username' => ['filled', 'string', 'min:3', 'alpha_dash', 'unique:'.User::class],
            'email' => ['filled', 'email', 'unique:'.User::class],
            'is_active' => ['filled', 'bool'],
            'profile.full_name' => ['filled', 'string', 'min:2'],
            'profile.bio' => ['filled', 'string', 'min:3'],
            'profile.location_id' => ['filled', 'exists:locations,id'],
            'profile.category_id' => ['filled', 'exists:categories,id'],
            'profile.website' => ['filled', 'string', 'min:3'],
            'profile.birthdate' => ['filled', 'date_format:Y-m-d'],
            'profile.gender' => ['filled', 'in:male,female'],
            'profile.payment_available' => ['filled', 'boolean'],
            'profile.private' => ['filled', 'boolean'],
            'profile.verified' => ['filled', 'boolean'],
            'profile.profile_image' => ['filled', 'image'],
        ];
    }

    /**
     * Get the custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'phone.filled' => 'Telefon belgi girizilen bolmalydyr.',
            'phone.integer' => 'Telefon belgi diňe sanlardan durmalydyr.',
            'phone.unique' => 'Bu telefon belgi eýýäm bar.',
            'username.filled' => 'Ulanyjy ady girizilen bolmalydyr.',
            'username.string' => 'Ulanyjy ady dogry görnüşde giriziň.',
            'username.min' => 'Ulanyjy ady azyndan 3 harpdan durmalydyr.',
            'username.alpha_dash' => 'Ulanyjy ady diňe harplardan, sanlardan, tire we aşaky çyzykdan durmalydyr.',
            'username.unique' => 'Bu ulanyjy ady eýýäm bar.',
            'email.filled' => 'Email adresi girizilen bolmalydyr.',
            'email.email' => 'Dogry email adresini giriziň.',
            'email.unique' => 'Bu email adres eýýäm bar.',
            'is_active.filled' => 'Aktiwlik ýagdaýy girizilen bolmalydyr.',
            'is_active.bool' => 'Aktiwlik ýagdaýy dogry görnüşde bolmalydyr.',
            'profile.full_name.filled' => 'Doly ady girizilen bolmalydyr.',
            'profile.full_name.string' => 'Doly ady dogry görnüşde giriziň.',
            'profile.full_name.min' => 'Doly ady azyndan 2 harpdan durmalydyr.',
            'profile.bio.filled' => 'Bio girizilen bolmalydyr.',
            'profile.bio.string' => 'Bio dogry görnüşde giriziň.',
            'profile.bio.min' => 'Bio azyndan 3 harpdan durmalydyr.',
            'profile.location_id.filled' => 'Ýerleşýän ýeri girizilen bolmalydyr.',
            'profile.location_id.exists' => 'Girizilen ýerleşýän ýer ID-si bar bolan ID-laryň biri däl.',
            'profile.category_id.filled' => 'Kategoriýa girizilen bolmalydyr.',
            'profile.category_id.exists' => 'Girizilen kategoriýa ID-si bar bolan ID-laryň biri däl.',
            'profile.website.filled' => 'Web sahypasy girizilen bolmalydyr.',
            'profile.website.string' => 'Web sahypasy dogry görnüşde giriziň.',
            'profile.website.min' => 'Web sahypasy azyndan 3 harpdan durmalydyr.',
            'profile.birthdate.filled' => 'Doglan güni girizilen bolmalydyr.',
            'profile.birthdate.date_format' => 'Doglan güni "YYYY-AA-GG" formatynda bolmalydyr.',
            'profile.gender.filled' => 'Jynsy girizilen bolmalydyr.',
            'profile.gender.in' => 'Jynsy diňe "erkek" ýa-da "aýal" bolup biler.',
            'profile.payment_available.filled' => 'Töleg mümkinçiligi girizilen bolmalydyr.',
            'profile.payment_available.boolean' => 'Töleg mümkinçiligi dogry görnüşde bolmalydyr.',
            'profile.private.filled' => 'Şahsylyk ýagdaýy girizilen bolmalydyr.',
            'profile.private.boolean' => 'Şahsylyk ýagdaýy dogry görnüşde bolmalydyr.',
            'profile.verified.filled' => 'Tassyklama ýagdaýy girizilen bolmalydyr.',
            'profile.verified.boolean' => 'Tassyklama ýagdaýy dogry görnüşde bolmalydyr.',
            'profile.profile_image.filled' => 'Profil suraty girizilen bolmalydyr.',
            'profile.profile_image.image' => 'Profil suraty surat görnüşinde bolmalydyr.',
        ];
    }
}
