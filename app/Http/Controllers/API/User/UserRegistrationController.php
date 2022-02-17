<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserWaitList;
use Illuminate\Http\Request;

class UserRegistrationController extends Controller
{
    public function createWaitListSignup(Request $request)
    {
        $email = $request->input('email');
        $textResponse = $request->input('textResponse');
        $textQuestion = "what kind of music do you like?";

        if (UserWaitList::emailAlreadySignedUp($email)) {
            return response()->json([
                'code' => 400,
                'message' => "Email already signed up",
            ]);
        }

        UserWaitList::createSignup($email, $textQuestion, $textResponse);

        return response()->json([
            'code' => 200,
            'message' => "You are now signed up on the wait list!",
        ]);
    }
}
