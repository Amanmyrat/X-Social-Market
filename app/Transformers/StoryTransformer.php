<?php

namespace App\Transformers;

use App\Models\Story;
use League\Fractal\TransformerAbstract;

class StoryTransformer extends TransformerAbstract
{
    public function transform(Story $story): array
    {
        return [
            'id' => $story->id,
            'image' => url('uploads/stories/'.$story->image),
        ];
    }

}
