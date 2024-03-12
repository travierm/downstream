<?php

namespace App\Repos\Types\Media;

class MediaItem
{
    public function __construct(
        public int $id,
        public string $origin,
        public string $type,
        public string $subtype,
        public string $index,
        public string $title,
        public string $thumbnail,
        public string $spotify_id,
        public int $user_id,
        public string $created_at,
        public string $updated_at,
        public ?string $deleted_at,
        public int $media_id,
        public string $pushed_at
    ) {
    }
}
