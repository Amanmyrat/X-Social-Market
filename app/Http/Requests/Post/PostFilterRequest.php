<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class PostFilterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $allowedSortOptions = [
            'default',
            'price',
            '-price',
        ];
        $allowedSortOptionsString = implode(',', $allowedSortOptions);

        return [
            'user_id' => ['filled', 'exists:users,id'],
            'price_min' => ['filled', 'integer'],
            'price_max' => ['filled', 'integer'],

            'brands' => ['filled', 'array'],
            'brands.*' => ['filled', 'int', 'exists:brands,id'],

            'colors' => ['filled', 'array'],
            'colors.*' => ['filled', 'int', 'exists:colors,id'],

            'sizes' => ['filled', 'array'],
            'sizes.*' => ['filled', 'int', 'exists:sizes,id'],

            'sort' => ['filled', 'in:'.$allowedSortOptionsString],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.filled' => 'Ulanyjy ID-si girizilen bolmalydyr.',
            'user_id.exists' => 'Girizilen ulanyjy ID bar bolan ID-laryň biri däl.',
            'price_min.filled' => 'Iň az baha girizilen bolmalydyr.',
            'price_min.integer' => 'Iň az baha diňe sanlardan durmalydyr.',
            'price_max.filled' => 'Iň köp baha girizilen bolmalydyr.',
            'price_max.integer' => 'Iň köp baha diňe sanlardan durmalydyr.',
            'brands.filled' => 'Markalar girizilen bolmalydyr.',
            'brands.array' => 'Markalar sanaw görnüşinde bolmalydyr.',
            'brands.*.filled' => 'Her bir marka girizilen bolmalydyr.',
            'brands.*.int' => 'Her bir marka ID diňe sanlardan durmalydyr.',
            'brands.*.exists' => 'Her bir marka ID bar bolan ID-laryň biri däl.',
            'colors.filled' => 'Reňkler girizilen bolmalydyr.',
            'colors.array' => 'Reňkler sanaw görnüşinde bolmalydyr.',
            'colors.*.filled' => 'Her bir reňk girizilen bolmalydyr.',
            'colors.*.int' => 'Her bir reňk ID diňe sanlardan durmalydyr.',
            'colors.*.exists' => 'Her bir reňk ID bar bolan ID-laryň biri däl.',
            'sizes.filled' => 'Ölçegler girizilen bolmalydyr.',
            'sizes.array' => 'Ölçegler sanaw görnüşinde bolmalydyr.',
            'sizes.*.filled' => 'Her bir ölçeg girizilen bolmalydyr.',
            'sizes.*.int' => 'Her bir ölçeg ID diňe sanlardan durmalydyr.',
            'sizes.*.exists' => 'Her bir ölçeg ID bar bolan ID-laryň biri däl.',
            'sort.filled' => 'Tertipleme girizilen bolmalydyr.',
            'sort.in' => 'Tertipleme diňe bu opsiýalaryň biri bolup biler: default, price, -price.',
        ];
    }

}
