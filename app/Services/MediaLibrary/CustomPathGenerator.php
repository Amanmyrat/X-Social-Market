<?php

namespace App\Services\MediaLibrary;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return $this->baseDir($media).'/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->baseDir($media).'/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->baseDir($media).'/responsive-images/';
    }

    protected function baseDir(Media $media): string
    {
        $year = $media->created_at->format('Y');
        $month = $media->created_at->format('m');
        $day = $media->created_at->format('d');
        $hashedId = Str::limit(md5($media->model_id), 12, '');

        return "$year/$month/$day/$hashedId";
    }
}
