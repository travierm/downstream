<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CypressTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $userId = DB::table('users')->where('email', 'test@gmail.com')->pluck('id')->first();
        if(!$userId) {
            $userId = DB::table('users')->insertGetId([
                'display_name' => Str::random(10),
                'email' => 'test@gmail.com',
                'hash' => Str::random(20),
                'api_token' => Str::random(20),
                'password' => Hash::make('password'),
            ]);
        }

        /*$mediaId = DB::table('media')->where('index', '8kUNIXRY9io')->pluck('id')->first();
        if(!$mediaId) {
            $mediaId = DB::table('media')->insertGetId([
                'index' => '8kUNIXRY9io',
                'type' => 'youtube',
                'subtype' => 'video',
                'origin' => 'youtube#search',
                'title' => 'Kid Cudi - Mr. Solo Dolo III (Official Visualizer)',
                'thumbnail' => 'https://i.ytimg.com/vi/8kUNIXRY9io/sddefault.jpg',
                'meta' => json_encode([]),
                'user_id' => $userId
            ]);
        }    

        DB::table('user_media')->insert([
            'user_id' => $userId,
            'media_id' => $mediaId
        ]);*/
    }
}
