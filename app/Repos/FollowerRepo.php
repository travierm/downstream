<?php

namespace App\Repos;

use App\Models\User;
use App\Models\Media;
use App\Models\UserMedia;
use Illuminate\Support\Collection;

class FollowerRepo
{
    /**
     * @param User $user
     * @param integer $numOfItems
     * @return Collection<Media>
     */
    public function getRecentlyCollectedItemsFromFollowing(User $user, int $numOfItems = 50): Collection
    {
        $followingUserIds = $user->following()->pluck('follow_id');
        $followingCollectedMediaItems = UserMedia::whereIn('user_id', $followingUserIds)
            ->with('media', 'user')
            ->orderBy('created_at', 'DESC')
            ->limit($numOfItems)
            ->distinct('media_id')
            ->get();

        return $followingCollectedMediaItems->map(function (UserMedia $item) {
            $media = $item->media;

            $media->objectMeta = [
                'collected_by' => $item->user->display_name,
                'collected_at' => $item->created_at->diffForHumans(),
            ];

            return $media;
        });
    }
}
