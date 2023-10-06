<?php

namespace App\Transformers;

use App\Models\Story;
use JetBrains\PhpStorm\ArrayShape;
use League\Fractal\TransformerAbstract;

class StoryTransformer extends TransformerAbstract
{
    #[ArrayShape(['id' => "mixed", 'image' => "mixed"])]
    public function transform(Story $story): array
    {
        return [
            'id' => $story->id,
            'image' => url('uploads/stories/'.$story->image),
        ];
    }

}
