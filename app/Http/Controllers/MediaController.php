<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    public function show(Media $media, $conversion = null): StreamedResponse
    {
        $disk = Storage::disk($media->disk);
        $path = $conversion ? $media->getPath($conversion) : $media->getPath();

        if (! $disk->exists($path)) {
            abort(404);
        }

        $stream = $disk->readStream($path);

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type' => $media->mime_type,
            'Content-Disposition' => 'inline; filename="' . $media->file_name . '"',
        ]);
    }
}
