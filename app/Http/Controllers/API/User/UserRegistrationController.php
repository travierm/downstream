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
                'message' => "You have already joined the waiting list",
            ], 400);
        }

        UserWaitList::createSignup($email, $textQuestion, $textResponse);

        return response()->json([
            'message' => "You have successfully joined the waiting list!",
        ], 200);
    }
}
