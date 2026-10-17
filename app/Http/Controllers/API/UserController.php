<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function getUserInfo(Request $req)
    {
        return $req->user();
    }

    public function getActiveUsers()
    {
        return User::select('id', 'display_name', 'hash', 'created_at')
            ->withCount([
                'media',
                'followers',
                'spotifyImports' => fn ($query) => $query->where('imported', true),
            ])
            ->public()
            ->orderBy('media_count', 'desc')
            ->limit(9)
            ->get()
            ->makeHidden('has_spotify_connection');
    }

}
