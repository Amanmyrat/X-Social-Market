<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrivacyPolicyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'content_en' => $this->resource->content_en,
            'content_ru' => $this->resource->content_ru,
            'content_tk' => $this->resource->content_tk,
        ];
    }
}
