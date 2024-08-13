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
            'username' => ['filled', 'string', 'min:3', 'regex:/^[a-zA-Z0-9\._]+$/i', 'unique:' . User::class],
            'email' => ['filled', 'email', 'unique:' . User::class],
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

    public function messages(): array
    {
        return [
            'username.filled' => 'Ulanyjy ady girizilen bolmalydyr.',
            'username.string' => 'Ulanyjy ady dogry görnüşde giriziň.',
            'username.min' => 'Ulanyjy ady azyndan 3 harpdan durmalydyr.',
            'username.regex' => 'Ulanyjy ady diňe harplardan, sanlardan, nokatlardan we aşaky çyzyklardan durmalydyr.',
            'username.unique' => 'Bu ulanyjy ady eýýäm bar.',
            'email.filled' => 'Email adresi girizilen bolmalydyr.',
            'email.email' => 'Dogry email adresini giriziň.',
            'email.unique' => 'Bu email adres eýýäm bar.',
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
            'profile.profile_image.filled' => 'Profil suraty girizilen bolmalydyr.',
            'profile.profile_image.image' => 'Profil suraty surat görnüşinde bolmalydyr.',
        ];
    }

}
