<?php

namespace App\Console\Commands;

use DB;
use Artisan;
use App\Media;
use App\Artist;
use App\MediaMeta;
use App\UserMedia;
use App\Media\YouTube;
use App\Media\YouTubeV2;
use App\Services\SpotifyAPI;
use Illuminate\Console\Command;
use App\Models\UserSpotifyToken;
use App\Services\Spotify\SpotifySyncService;

class SpotifySync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Spotify User Commands';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(public SpotifySyncService $spotifySyncService)
    {
        parent::__construct();

        //$this->spotifySyncService = new SpotifySyncService();
    }

    public function handle()
    {
        $spotifyTokens = UserSpotifyToken::all();

        $this->info(sprintf('found %s spotify tokens', count($spotifyTokens)));

        foreach ($spotifyTokens as $token) {
            $this->spotifySyncService->syncByToken($token);
        }
    }
}
