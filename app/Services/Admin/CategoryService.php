<?php

namespace App\Services\Admin;

use App\Models\Category;
use DB;
use Exception;
use Throwable;

class CategoryService
{
    /**
     * @throws Throwable
     */
    public function create(array $data): void
    {
        DB::transaction(function () use ($data) {
            $category = Category::create($data);
            $category->addMedia($data['icon'])->toMediaCollection('category_images');
        });
    }

    /**
     * @throws Exception
     */
    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        if (isset($data['icon'])) {
            try {
                $existingMedia = $category->getFirstMedia('category_images');

                $existingMedia?->delete();

                $category->addMedia($data['icon'])->toMediaCollection('category_images');
            } catch (Exception $exception) {
                throw new Exception("Error updating category media: " . $exception->getMessage());
            }
        }

        return $category;
    }

}
