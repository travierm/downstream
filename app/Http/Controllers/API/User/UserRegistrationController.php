<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserInviteCode;
use App\Models\UserWaitList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRegistrationController extends Controller
{
    public function registerUser(Request $request)
    {
        $request->validate([
            'invite_code' => 'required',
            'email' => 'required',
            'display_name' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        $input = $request->input();
        $email = $input['email'];
        $inviteCode = $input['invite_code'];

        // Check that invite code is valid
        if (!UserInviteCode::codeIsValid($inviteCode)) {
            return response()->json([
                'message' => "Invalid or used invite code",
            ], 400);
        }

        $dupeUser = User::where('email', $email)->exists();
        if ($dupeUser) {
            return response()->json([
                'message' => "Invalid email or account already exists",
            ], 400);
        }

        // Create the user account
        $user = User::create([
            'hash' => Str::random(40),
            'display_name' => $input['display_name'],
            'password' => Hash::make($input['password']),
            'email' => $email,
            'api_token' => Str::random(60),
        ]);

        if (!$user) {
            return response()->json([
                'message' => "Failed to create user account",
            ], 500);
        }

        // Mark user email as signed up on the waiting list
        UserWaitList::signupEmail($email);
        // Update the invite code to be used by our created user
        UserInviteCode::useInvite($user->id, $inviteCode);

        return response()->json([
            'message' => "Succesfully created your account!",
        ], 200);
    }

    public function createWaitListSignup(Request $request)
    {
        $email = $request->input('email');
        $textResponse = $request->input('textResponse');
        $textQuestion = "what kind of music do you like?";

        if (UserWaitList::emailAlreadySignedUp($email)) {
            return response()->json([
                'message' => "You have already joined the waiting list",
            ], 400);
        }

        UserWaitList::createSignup($email, $textQuestion, $textResponse);

        return response()->json([
            'message' => "You have successfully joined the waiting list!",
        ], 200);
    }
}
