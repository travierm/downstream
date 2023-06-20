<?php

namespace App\Repos;

use App\Models\User;
use App\Models\Media;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserStatsRepo
{
    /**
     * @param User $user
     * @param integer $limit
     * @return Array<{media_id:integer,title:string,thumbnail:string,plays:integer}>]
     */
    public function getTopPlayedTracks(User $user, int $limit = 10): array
    {
        /** @var Collection<{media_id:integer,total:integer}> $topTracks */
        $topTracks = $user->userMediaPlays()
            ->select('media_id', DB::raw('count(*) as plays'))
            ->groupBy('media_id')
            ->orderBy('plays', 'desc')
            ->limit($limit)
            ->get();


        return array_map(function ($item) {
            $media = Media::find($item['media_id']);

            return [
                'media_id' => $media->id,
                'title' => $media->title,
                'thumbnail' => $media->thumbnail,
                'plays' => $item['plays'],
            ];
        }, $topTracks->toArray());
    }
}
