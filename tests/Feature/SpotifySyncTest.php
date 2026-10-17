<?php

namespace Tests\Feature;

use App\Console\Commands\SpotifySyncClean;
use App\Models\User;
use App\Models\UserSpotifyToken;
use App\Services\Spotify\SpotifySyncService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use SpotifyWebAPI\SpotifyWebAPI;
use SpotifyWebAPI\SpotifyWebAPIException;
use Tests\CreatesApplication;

class SpotifySyncTest extends TestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'logging.default' => 'null']);
        DB::purge('sqlite');
        Schema::create('user_spotify_tokens', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('access_token');
            $table->string('refresh_token');
            $table->timestamps();
        });
        DB::table('user_spotify_tokens')->insert([
            ['user_id' => 2, 'access_token' => 'invalid-other-user', 'refresh_token' => 'invalid'],
            ['user_id' => 1, 'access_token' => 'valid-current-user', 'refresh_token' => 'valid'],
        ]);
        $user = new User;
        $user->id = 1;
        $this->actingAs($user);
    }

    public function test_manual_sync_cleans_only_the_current_users_playlist(): void
    {
        $api = Mockery::mock(SpotifyWebAPI::class);
        $api->shouldReceive('me')->andReturn((object) ['id' => 'current-user']);
        $api->shouldReceive('getUserPlaylists')->andReturn((object) ['items' => [
            (object) ['id' => 'import-playlist', 'name' => 'DS Import'],
        ]]);
        $api->shouldReceive('getPlaylistTracks')->andReturn((object) ['items' => []]);
        $api->shouldReceive('deletePlaylistTracks')->never();
        $invalidApi = Mockery::mock(SpotifyWebAPI::class);
        $invalidApi->shouldReceive('me')->andThrow(new SpotifyWebAPIException('Missing/invalid/expired access token', 401));
        $command = Mockery::mock(SpotifySyncClean::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $command->__construct();
        $command->shouldReceive('apiForToken')
            ->andReturnUsing(fn (UserSpotifyToken $token) => $token->user_id === 1 ? $api : $invalidApi);

        Artisan::getFacadeRoot()->registerCommand($command);
        $sync = Mockery::mock(SpotifySyncService::class);
        $sync->shouldReceive('syncByToken')->once()->with(Mockery::on(fn ($token) => $token->user_id === 1));
        $this->app->instance(SpotifySyncService::class, $sync);

        $this->getJson('/api/spotify/run-sync')->assertOk();
    }

    public function test_manual_sync_reports_cleanup_failures(): void
    {
        $api = Mockery::mock(SpotifyWebAPI::class);
        $api->shouldReceive('me')->once()->andThrow(new SpotifyWebAPIException('Connection expired', 401));
        $command = Mockery::mock(SpotifySyncClean::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $command->__construct();
        $command->shouldReceive('apiForToken')->andReturn($api);
        Artisan::getFacadeRoot()->registerCommand($command);
        $sync = Mockery::mock(SpotifySyncService::class);
        $sync->shouldReceive('syncByToken')->once();
        $this->app->instance(SpotifySyncService::class, $sync);

        $this->getJson('/api/spotify/run-sync')->assertStatus(502)
            ->assertJsonPath('message', 'Tracks were synced, but the Spotify playlist could not be cleaned. Please try again.');
    }
}
