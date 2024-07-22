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
            'price' => ['sometimes', 'int'],
            'description' => ['sometimes', 'string', 'max:255'],
            'location' => ['sometimes', 'string', 'max:255'],
            'can_comment' => ['sometimes', 'boolean'],
            'medias' => ['sometimes', 'array', 'max:8'],
            'medias.*' => ['sometimes', 'file', 'mimes:jpg,jpeg,png,mp4,webp'],

            /**
             * Required if category has product true
             *
             * @example {
             *   "product": {
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
             * }
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
}
