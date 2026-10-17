<?php

namespace App\Console\Commands;

use App\Models\UserSpotifyToken;
use App\Services\Spotify\SpotifySyncService;
use Illuminate\Console\Command;

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
    protected $description = 'Import Spotify playlists for all connected users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(public SpotifySyncService $spotifySyncService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $failed = false;
        $spotifyTokens = UserSpotifyToken::all();

        $this->info(sprintf('found %s spotify tokens', count($spotifyTokens)));

        foreach ($spotifyTokens as $token) {
            try {
                $this->spotifySyncService->syncByToken($token);
                if ($this->call('spotify:sync-clean', ['--user-id' => $token->user_id]) !== self::SUCCESS) {
                    $failed = true;
                }
            } catch (\Throwable $e) {
                $failed = true;
                lc()->error('Spotify sync failed', ['user_id' => $token->user_id, 'message' => $e->getMessage()]);
                $this->error('Spotify sync failed for user '.$token->user_id);
            }
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
