<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserRegistrationController extends Controller
{
    public function registerUser(Request $request)
    {
        $input = $request->validate([
            'email' => 'required|string|email|max:255|unique:users,email',
            'display_name' => 'required|string|max:255',
            'password' => 'required|string|min:6|max:255|confirmed',
            'cf-turnstile-response' => 'required|string|max:2048',
        ]);

        $hostnames = config('services.turnstile.hostnames', []);
        if (app()->environment('production')) {
            $hostnames = array_diff($hostnames, ['localhost', '127.0.0.1', '::1']);
        }

        if (! config('services.turnstile.secret_key') || ! $hostnames) {
            return response()->json([
                'message' => 'Registration verification is unavailable. Please try again later.',
            ], 503);
        }

        try {
            $verification = Http::asForm()->timeout(10)->post(
                'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                [
                    'secret' => config('services.turnstile.secret_key'),
                    'response' => $input['cf-turnstile-response'],
                    'remoteip' => $request->ip(),
                ]
            );
        } catch (ConnectionException $exception) {
            return response()->json([
                'message' => 'Registration verification is unavailable. Please try again.',
            ], 503);
        }

        if (! $verification->successful()) {
            return response()->json([
                'message' => 'Registration verification is unavailable. Please try again.',
            ], 503);
        }

        if ($verification->json('success') !== true
            || $verification->json('action') !== 'register'
            || ! in_array($verification->json('hostname'), $hostnames, true)) {
            throw ValidationException::withMessages([
                'cf-turnstile-response' => 'Please complete the verification again.',
            ]);
        }

        User::create([
            'hash' => Str::random(40),
            'display_name' => $input['display_name'],
            'password' => Hash::make($input['password']),
            'email' => $input['email'],
            'api_token' => Str::random(60),
        ]);

        return response()->json([
            'message' => 'Successfully created your account!',
        ], 200);
    }
}
