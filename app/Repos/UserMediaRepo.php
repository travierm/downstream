<?php

namespace App\Repos;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Repos\Types\Media\MediaItem;

class UserMediaRepo
{
    /**
     * @param integer $userId
     * @param integer $start
     * @param integer $offset
     * @return Collection<MediaItem>
     */
    public function getCollectionSlice(int $userId, int $start = 0, int $offset = 0): Collection
    {
        $results = DB::table('media')
            ->join('user_media', 'user_media.media_id', '=', 'media.id')
            ->where('user_media.user_id', $userId)
            ->whereNull('user_media.deleted_at')
            ->skip($start)
            ->take($offset)
            ->orderBy('user_media.created_at', 'DESC')
            ->get();

        return $results;
    }
}
