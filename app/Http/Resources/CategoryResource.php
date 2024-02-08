<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\Pure;

class CategoryResource extends JsonResource
{
    private bool $detailsEnabled;

    #[Pure]
    public function __construct(Category $resource, bool $detailsEnabled = false)
    {
        parent::__construct($resource);
        $this->detailsEnabled = $detailsEnabled;
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return $this->detailsEnabled ? [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'icon' => url('uploads/categories/'.$this->icon),
            'is_active' => $this->is_active,
            'has_product' => $this->has_product,
            'posts_count' => $this->posts_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ] : [
            'id' => $this->id,
            'title' => $this->title,
            'icon' => url('uploads/categories/'.$this->icon),
            'is_active' => $this->is_active,
            'posts_count' => $this->posts_count,
        ];
    }
}
