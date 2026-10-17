<?php

namespace Tests\Feature;

use App\Http\Controllers\API\UserController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\CreatesApplication;

class UserDirectoryTest extends TestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('display_name');
            $table->string('hash');
            $table->boolean('private')->default(false);
            $table->timestamps();
        });
        Schema::create('user_media', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->softDeletes();
        });
        Schema::create('user_spotify_imports', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->boolean('imported');
        });
        Schema::create('followers', function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('follow_id');
        });
    }

    public function test_directory_counts_completed_imports_and_followers_per_public_user(): void
    {
        foreach (range(1, 12) as $id) {
            DB::table('users')->insert([
                'id' => $id, 'display_name' => "Collector $id", 'hash' => "user-$id",
                'private' => $id === 12, 'created_at' => '2021-06-15 00:00:00',
            ]);
        }
        DB::table('user_media')->insert([
            ['user_id' => 2, 'deleted_at' => null],
            ['user_id' => 2, 'deleted_at' => null],
            ['user_id' => 2, 'deleted_at' => '2026-01-01 00:00:00'],
            ['user_id' => 12, 'deleted_at' => null],
        ]);
        DB::table('user_spotify_imports')->insert([
            ['user_id' => 2, 'imported' => true],
            ['user_id' => 2, 'imported' => true],
            ['user_id' => 2, 'imported' => false],
            ['user_id' => 1, 'imported' => true],
        ]);
        DB::table('followers')->insert([
            ['user_id' => 1, 'follow_id' => 2],
            ['user_id' => 3, 'follow_id' => 2],
            ['user_id' => 2, 'follow_id' => 3],
        ]);

        $users = app(UserController::class)->getActiveUsers()->toArray();

        $this->assertCount(9, $users);
        $this->assertSame(2, $users[0]['id']);
        $this->assertEquals(2, $users[0]['media_count']);
        $this->assertEquals(2, $users[0]['spotify_imports_count']);
        $this->assertEquals(2, $users[0]['followers_count']);
        $this->assertStringStartsWith('2021-06-15', $users[0]['created_at']);
        $this->assertNotContains(12, array_column($users, 'id'));
        $this->assertArrayNotHasKey('has_spotify_connection', $users[0]);
        $emptyCollector = collect($users)->firstWhere('id', 4);
        $this->assertEquals(0, $emptyCollector['spotify_imports_count']);
        $this->assertEquals(0, $emptyCollector['followers_count']);
    }
}
