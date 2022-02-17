<?php

namespace Tests;

use App\Models\Playlist;
use App\Models\PlaylistItem;
use App\Models\User;
use App\Models\UserMedia;
use App\Models\UserWaitList;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): Void
    {
        global $setupAlreadyRan;
        /**
         * This disables the exception handling to display the stacktrace on the console
         * the same way as it shown on the browser
         */
        parent::setUp();
        $this->withoutExceptionHandling();

        if (!$setupAlreadyRan) {
            $this->runOnce();
        }

        $setupAlreadyRan = true;
    }

    private function runOnce()
    {
        $testUsers = User::where('display_name', 'LIKE', 'ds_test_user_%');

        UserWaitList::truncate();

        foreach ($testUsers as $user) {
            $userId = $user->id;

            UserMedia::where('user_id', $userId)->delete();
            Playlist::where('created_by', $userId)->delete();
            PlaylistItem::where('created_by', $userId)->forceDelete();

            $user->delete();
        }
    }
}
