<?php

namespace App\Console\Commands;

use App\Models\Media;
use App\Models\UserMedia;
use App\Models\UserSpotifyToken;
use App\Services\Spotify\ImportPlaylist;
use App\Services\SpotifyAPI;
use Illuminate\Console\Command;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifySyncClean extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify:sync-clean {--user-id= : Only clean this user\'s import playlist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean users DS Import of items that already exists in collection';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $lc = lc();
        $lc->info('starting spotify:sync-clean');
        $failed = false;

        $usersWithTokens = UserSpotifyToken::query()
            ->when($this->option('user-id') !== null, function ($query) {
                $query->where('user_id', $this->option('user-id'));
            })
            ->get();
        $lc->info('found '.count($usersWithTokens).' users to clean DS Import playlist for');

        foreach ($usersWithTokens as $token) {
            try {
                $api = $this->apiForToken($token);
                $playlists = new ImportPlaylist($api);
                $importPlaylist = $playlists->find($api->me()->id);
                if (! $importPlaylist) {
                    $lc->info('could not find DS Import playlist for user:'.$token->user_id);

                    continue;
                }

                // Finish reading before removing tracks, since removals change page offsets.
                $tracksForTrash = $this->cleanTracks($token->user_id, $playlists->tracks($importPlaylist->id));
                $lc->info('deleting '.count($tracksForTrash).' from users playlist');
                foreach (array_chunk($tracksForTrash, 100) as $tracks) {
                    $api->deletePlaylistTracks($importPlaylist->id, ['tracks' => $tracks]);
                }
            } catch (\Throwable $e) {
                $failed = true;
                $lc->error('Spotify cleanup failed', ['user_id' => $token->user_id, 'message' => $e->getMessage()]);
            }
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    protected function apiForToken(UserSpotifyToken $token): SpotifyWebAPI
    {
        return SpotifyAPI::getInstanceWithToken($token);
    }

    public function cleanTracks($userId, $tracks)
    {
        $trackTrashcan = [];
        foreach ($tracks as $track) {
            if (! $track->track) {
                continue;
            }
            $trackId = $track->track->id;
            if (! $trackId) {
                continue;
            }
            $trackName = $track->track->name;
            $trackArtist = $track->track->artists[0]->name;
            $trackSearchQuery = $trackArtist.' '.$trackName;
            $this->info("Checking $trackSearchQuery against the meta table");

            $media = Media::where('spotify_id', $trackId)
                ->orWhereHas('meta', fn ($query) => $query->where('spotify_id', $trackId))
                ->first();
            if (! $media) {
                $this->info('Not a media item');

                continue;
            }

            $userMedia = UserMedia::where('media_id', $media->id)
                ->where('user_id', $userId)
                ->first();

            // track exists in media and in users collection
            if ($media && $userMedia) {
                $this->info("Removing $trackSearchQuery from playlist");
                $trackTrashcan[] = ['uri' => $track->track->uri];
            }
        }

        return $trackTrashcan;
    }
}
