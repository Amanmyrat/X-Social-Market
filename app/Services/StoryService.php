<?php

namespace App\Services;

use App\Http\Requests\StoryRequest;
use App\Models\Story;
use Carbon\Carbon;

class StoryService
{
    public static function create(StoryRequest $request): void
    {
        $validated = $request->validated();

        if ($validated['type'] == 'basic') {
            $imageName = $request->user()->phone.'-'.time().'.'.$request->image->getClientOriginalExtension();
            $validated['image']->move(public_path('uploads/stories'), $imageName);
            $validated['image'] = $imageName;
            $validated['post_id'] = null;
        } else {
            $validated['image'] = null;
        }

        Story::create(array_merge($validated, [
            'user_id' => $request->user()->id,
            'valid_until' => Carbon::now()->addYear(),
        ]));

    }
}
