<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
        if (! $userId) {
            $userId = DB::table('users')->insertGetId([
                'display_name' => Str::random(10),
                'email' => 'test@gmail.com',
                'hash' => Str::random(20),
                'api_token' => Str::random(20),
                'password' => Hash::make('password'),
            ]);
        }

        $mediaId = DB::table('media')->where('index', 'ppSY98RGyBU')->pluck('id')->first();
        if (! $mediaId) {
            $mediaId = DB::table('media')->insertGetId([
                'index' => 'ppSY98RGyBU',
                'type' => 'youtube',
                'subtype' => 'video',
                'origin' => 'youtube#search',
                'title' => 'Kodak Black - Calling My Spirit',
                'thumbnail' => 'https://i.ytimg.com/vi/8kUNIXRY9io/sddefault.jpg',
                'user_id' => $userId,
            ]);
        }

        DB::table('user_media')->insert([
            'user_id' => $userId,
            'media_id' => $mediaId,
        ]);
    }
}
