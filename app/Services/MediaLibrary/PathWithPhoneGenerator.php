<?php

namespace App\Services\MediaLibrary;

use App\Models\Post;
use App\Models\Story;
use App\Models\UserProfile;
use Exception;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;
use Storage;

class PathWithPhoneGenerator implements PathGenerator
{
    /**
     * @throws Exception
     */
    public function getPath(Media $media): string
    {
        return $this->baseDir($media).'/';
    }

    /**
     * @throws Exception
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->baseDir($media).'/conversions/';
    }

    /**
     * @throws Exception
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->baseDir($media).'/responsive-images/';
    }

    /**
     * @throws Exception
     */
    protected function baseDir(Media $media): string
    {
        $year = $media->created_at->format('Y');
        $month = $media->created_at->format('m');
        $day = $media->created_at->format('d');
        $hashedId = Str::limit(md5($media->model_id), 12, '');

        $userId = $media->model->user->id;
        $oldPhone = $media->model->user->phone;

        $disk = $this->getDiskForMedia($media);

        $newPath = "$year/$month/$day/$userId/$hashedId";
        $oldPath = "$year/$month/$day/$oldPhone/$hashedId";

        // Check if any files exist inside the old path
        $oldPathFiles = Storage::disk($disk)->files($oldPath);

        // If there are files in the old path, return it
        return !empty($oldPathFiles) ? $oldPath : $newPath;
    }


    /**
     * Determine the correct storage disk based on the media model type.
     * @throws Exception
     */
    private function getDiskForMedia(Media $media): string
    {
        return match ($media->model_type) {
            Story::class => 'stories',
            Post::class => 'posts',
            UserProfile::class => 'users',
            default => throw new Exception("No disk found for media ID: {$media->id}")
        };
    }

}
