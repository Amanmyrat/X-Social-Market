<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserListRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $allowedSortOptions = [
            'default',
            'username',
            '-username',
            'is_active',
            '-is_active',
            'created_at',
            '-created_at',
        ];
        $allowedSortOptionsString = implode(',', $allowedSortOptions);

        return [
            'limit' => ['filled', 'integer'],
            'type' => ['string', 'in:user,seller'],
            'sort' => ['filled', 'in:' . $allowedSortOptionsString],
        ];
    }

    public function messages(): array
    {
        return [
            'limit.filled' => 'Çäk girizilen bolmalydyr.',
            'limit.integer' => 'Çäk diňe sanlardan durmalydyr.',
            'type.filled' => 'Görnüşi girizilen bolmalydyr.',
            'type.in' => 'Görnüşi diňe "simple" ýa-da "clothing" bolup biler.',
            'sort.filled' => 'Tertipleme girizilen bolmalydyr.',
            'sort.string' => 'Tertipleme dogry görnüşde bolmalydyr.',
            'sort.in' => 'Tertipleme üçin dogry opsiýany saýlaň: default, title, -title, is_active, -is_active, products_count, -products_count.',
        ];
    }
}
