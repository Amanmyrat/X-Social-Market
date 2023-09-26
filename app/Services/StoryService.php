<?php

namespace App\Services;

use App\Models\Story;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StoryService
{
    public static function create(Request $request): void
    {
        $validated = $request->validate([
            'image' => ['required', 'image'],
        ]);

        $imageName = $request->user()->phone.'-'.time().'.'.$request->image->getClientOriginalExtension();
        $validated['image']->move(public_path('uploads/stories'), $imageName);
        $validated['image'] = $imageName;

        Story::create(array_merge($validated, [
            'user_id' => $request->user()->id,
            'valid_until' => Carbon::now()->addDay(),
        ]));

    }
}
