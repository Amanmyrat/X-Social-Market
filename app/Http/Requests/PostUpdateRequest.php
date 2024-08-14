<?php

namespace App\Http\Requests;

use App\Rules\ProductDetailsValidation;
use Illuminate\Foundation\Http\FormRequest;

class PostUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'int', 'exists:categories,id'],
            'caption' => ['sometimes', 'string', 'max:255'],
            'price' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string', 'max:255'],
            'location' => ['sometimes', 'string', 'max:255'],
            'can_comment' => ['sometimes', 'boolean'],
            'medias' => ['sometimes', 'array', 'max:8'],
            'medias.*' => ['sometimes', 'file', 'mimes:jpg,jpeg,png,mp4,webp,gif,mpeg4,mov'],

            /**
             * Required if category has product true
             *
             * @example {
             *   {
             *     "brand_id": 1,
             *     "gender": "male",
             *     "options": {
             *       "colors": [{
             *         "color_id": 1,
             *         "sizes": [{
             *           "size_id": 1,
             *           "price": 100,
             *           "stock": 101
             *         }]
             *       }]
             *     }
             *   }
             */
            'product' => [new ProductDetailsValidation((int) request('category_id'))],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $category_id = $this->category_id;
            $product = $this->product;

            // Instantiate your custom rule manually
            $rule = new ProductDetailsValidation($category_id);

            // Manually call the passes method
            if (! $rule->passes('product', $product)) {
                $validator->errors()->add('product', $rule->message());
            }
        });
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategoriýa ID-si hökmanydyr.',
            'category_id.int' => 'Kategoriýa ID-si diňe sanlardan durmalydyr.',
            'category_id.exists' => 'Saýlanan kategoriýa bar bolan ID-laryň biri däl.',
            'caption.string' => 'Mazmun dogry görnüşde giriziň.',
            'caption.max' => 'Mazmun iň köp 255 harpdan durmalydyr.',
            'price.numeric' => 'Bahasy san görnüşinde bolmalydyr.',
            'description.string' => 'Beýany dogry görnüşde giriziň.',
            'description.max' => 'Beýan iň köp 255 harpdan durmalydyr.',
            'location.string' => 'Ýerleşýän ýer dogry görnüşde giriziň.',
            'location.max' => 'Ýerleşýän ýer iň köp 255 harpdan durmalydyr.',
            'can_comment.boolean' => 'Teswir ýazylyp bilinýänligi dogry görnüşde bolmalydyr.',
            'medias.array' => 'Media faýllary sanaw görnüşinde bolmalydyr.',
            'medias.max' => 'Iň köp 8 media faýl goýulyp bilner.',
            'medias.*.file' => 'Her bir media faýl faýl görnüşinde bolmalydyr.',
            'medias.*.mimes' => 'Media faýllar şu formatlarda bolmalydyr: jpg, jpeg, png, mp4, webp, gif, mpeg4, mov.',
        ];
    }

}
