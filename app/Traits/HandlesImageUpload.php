<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait HandlesImageUpload
{
    public function storeImage(UploadedFile $file, string $basePath, string $bucket): array
    {
        $fileNameWithoutExt = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = 'jpg';
        $uniqueFileName = $fileNameWithoutExt.'-'.uniqid();

        $originalPath = "$basePath/$uniqueFileName/$uniqueFileName.$extension";
        Storage::disk($bucket)->put($originalPath, file_get_contents($file), 'public');
        $paths['original'] = Storage::disk($bucket)->url($originalPath);

        $resolutions = ['large' => 1024, 'medium' => 768, 'small' => 480, 'tiny' => 100];

        foreach ($resolutions as $key => $size) {
            $filePath = "$basePath/$uniqueFileName/$uniqueFileName-$key.webp";
            $image = Image::make($file->getRealPath());
            $image->resize($size, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            if ($key === 'tiny') {
                $image->blur();
            }
            Storage::disk($bucket)->put($filePath, (string) $image->encode('webp', 85), 'public');
            $paths[$key] = Storage::disk($bucket)->url($filePath);
        }

        return $paths;
    }
}
