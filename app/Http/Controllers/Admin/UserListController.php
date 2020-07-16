<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Media;
use App\User;
use App\UserMedia;

class UserListController extends Controller
{
    public function getList()
    {
        $users = User::orderBy('created_at', 'DESC')->get();

        foreach($users as &$user) {
      
            $user->collectionSize = UserMedia::where('user_id', $user->id)->count();

            if($user->collectionSize == 0) {
                continue;
            }

            $latestCollectedItem = UserMedia::where('user_id', $user->id)
                ->orderBy("created_at", "DESC")
                ->first();

            if($latestCollectedItem) {
                $user->lastCollectedItemDate = $latestCollectedItem->created_at->format('Y-m-d g:ia');
            }
            
        }

        return view('admin.users', [
            'users' => $users
        ]);
    }
}
