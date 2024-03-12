<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserInviteCode;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function getUserInfo(Request $req)
    {
        return $req->user();
    }

    public function getActiveUsers()
    {
        return User::withCount('media')
            ->public()
            ->orderBy('media_count', 'desc')
            ->limit(9)
            ->get();
    }

    public function generateInviteLink(Request $request)
    {
        $userId = $request->user()->id;

        $inviteCode = UserInviteCode::createInvite($userId);
        $link = env('APP_URL') . '/register?invite_code=' . $inviteCode->invite_code;

        return response()->json(['link' => $link], 200);
    }
}
