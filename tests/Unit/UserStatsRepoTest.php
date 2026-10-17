<?php

namespace Tests\Unit;

use App\Models\User;
use App\Repos\UserStatsRepo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\CreatesApplication;

class UserStatsRepoTest extends TestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Exercise the real query without using the configured application database.
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');

        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->softDeletes();
        });
        Schema::create('user_media_plays', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('media_id');
            $table->string('play_type');
            $table->timestamps();
        });
    }

    public function test_top_tracks_include_all_time_plays_and_rewinds_for_only_the_current_user(): void
    {
        DB::table('media')->insert([
            ['id' => 1, 'title' => 'Old favorite'],
            ['id' => 2, 'title' => 'Recent track'],
        ]);
        foreach ([
            [1, 1, 'click-to-play', now()->subYears(3)],
            [1, 1, 'rewind', now()->subYears(3)],
            [1, 2, 'click-to-play', now()],
            [2, 2, 'click-to-play', now()],
            [2, 2, 'rewind', now()],
        ] as [$userId, $mediaId, $type, $date]) {
            DB::table('user_media_plays')->insert([
                'user_id' => $userId,
                'media_id' => $mediaId,
                'play_type' => $type,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
        $user = new User();
        $user->id = 1;
        $repo = new UserStatsRepo();

        $tracks = $repo->getTopPlayedTracks($user);

        $this->assertSame([1, 2], array_column(array_column($tracks, 'media'), 'id'));
        $this->assertSame([2, 1], array_column($tracks, 'plays'));
        $this->assertCount(1, $repo->getTopPlayedTracks($user, 1));
    }
}
