<?php

namespace App\Transformers;

use App\Models\SpamType;
use League\Fractal\TransformerAbstract;

class SpamTypeTransformer extends TransformerAbstract
{
    public function transform(SpamType $type): array
    {
        return [
            'id' => $type->id,
            'name' => $type->name,
        ];
    }
}
