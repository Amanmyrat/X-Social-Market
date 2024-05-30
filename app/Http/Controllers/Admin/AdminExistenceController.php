<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckExistenceRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Location;
use App\Models\ReportType;
use App\Models\Size;
use Illuminate\Http\JsonResponse;

class AdminExistenceController extends Controller
{
    /**
     * Check existence
     *
     */
    public function checkExistence(CheckExistenceRequest $request): JsonResponse
    {
        $type = $request->type;
        $title = $request->title;

        // Mapping the type to the corresponding model class
        $modelMapping = [
            'Category'    => Category::class,
            'Brand'       => Brand::class,
            'Location'    => Location::class,
            'Color'       => Color::class,
            'Size'        => Size::class,
            'ReportType'  => ReportType::class,
        ];

        $modelClass = $modelMapping[$type] ?? null;

        if (!$modelClass) {
            return response()->json(['error' => 'Invalid type provided'], 400);
        }

        // Check if the title exists in the specified model
        $exists = $modelClass::whereRaw('LOWER(title) = LOWER(?)', [$title])->exists();

        return response()->json(['exists' => $exists]);
    }
}
