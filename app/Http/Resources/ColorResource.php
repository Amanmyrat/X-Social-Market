<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

class ColorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    #[ArrayShape(['id' => "mixed", 'title' => "mixed", 'code' => "mixed"])]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'code' => $this->code,
        ];
    }
}
