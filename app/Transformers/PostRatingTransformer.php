<?php

namespace App\Transformers;

use App\Models\PostRating;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class PostRatingTransformer extends TransformerAbstract
{


    public function transform(PostRating $rating): array
    {
        return [
            'id' => $rating->id,
            'user' => $rating->user,
            'rating' => $rating->rating,
            'date' => Carbon::parse($rating->created_at)->format('d.m.Y'),
        ];
    }
}
