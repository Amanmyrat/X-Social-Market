<?php

namespace App\Models\Concerns;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasMediaUrls
{
    /**
     * Get first media URLs for a given collection.
     * Detects image vs. video and returns appropriate conversions.
     *
     * @param string $collection
     * @param array $imageConversions e.g. ['large','medium','thumb']
     * @param string|null $videoThumbConversion e.g. 'video_thumb'
     * @return array|null
     */
    public function firstMediaUrls(
        string $collection,
        array $imageConversions = ['large','medium','thumb'],
        ?string $videoThumbConversion = 'video_thumb'
    ): ?array {
        if (!$this->hasMedia($collection)) {
            return null;
        }

        /** @var Media $media */
        $media = $this->getFirstMedia($collection);

        $urls = [
            'original_url' => route('media.show', ['media' => $media->id]),
        ];

        if (str_starts_with($media->mime_type, 'image')) {
            foreach ($imageConversions as $conv) {
                $urls["{$conv}_url"] = route('media.show', ['media' => $media->id, 'conversion' => $conv]);
            }
        } else {
            if ($videoThumbConversion) {
                $urls['video_thumb_url'] = route('media.show', [
                    'media' => $media->id,
                    'conversion' => $videoThumbConversion
                ]);
            }
        }

        return $urls;
    }

    /**
     * Get all media URLs in a collection (array of arrays).
     */
    public function allMediaUrls(
        string $collection,
        array $imageConversions = ['large','medium','thumb'],
        ?string $videoThumbConversion = 'video_thumb'
    ): ?array {
        if (!$this->hasMedia($collection)) {
            return null;
        }

        $out = [];
        foreach ($this->getMedia($collection) as $media) {
            $urls = [
                'original_url' => route('media.show', ['media' => $media->id]),
            ];

            if (str_starts_with($media->mime_type, 'image')) {
                foreach ($imageConversions as $conv) {
                    $urls["{$conv}_url"] = route('media.show', ['media' => $media->id, 'conversion' => $conv]);
                }
            } else {
                if ($videoThumbConversion) {
                    $urls['video_thumb_url'] = route('media.show', [
                        'media' => $media->id,
                        'conversion' => $videoThumbConversion
                    ]);
                }
            }

            $out[] = $urls;
        }

        return $out;
    }
}
