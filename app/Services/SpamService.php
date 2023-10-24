<?php

namespace App\Services;

use App\Models\SpamType;
use Illuminate\Http\Request;

class SpamService
{
    public static function create(Request $request): void
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
        ]);
        SpamType::create($validated);
    }
}
