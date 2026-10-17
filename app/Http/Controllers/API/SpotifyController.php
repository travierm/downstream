<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserSpotifyToken;
use App\Repos\SpotifyRepo;
use App\Services\Spotify\SpotifySyncService;
use App\Services\SpotifyAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SpotifyController extends Controller
{
    public function __construct(public SpotifyRepo $spotifyRepo, public SpotifySyncService $spotifySyncService) {}

    public function runSpotifySync()
    {
        $lc = getRequestLogContext();
        $lc->info('starting manual spotify sync');

        $spotifyToken = UserSpotifyToken::where('user_id', Auth::user()->id)->first();

        try {
            $this->spotifySyncService->syncByToken($spotifyToken);
        } catch (\Exception $e) {
            $lc->error('failed to complete spotify sync', [
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }

        if (Artisan::call('spotify:sync-clean', ['--user-id' => Auth::id()]) !== 0) {
            $lc->error('Spotify playlist cleanup failed');

            return response()->json([
                'message' => 'Tracks were synced, but the Spotify playlist could not be cleaned. Please try again.',
            ], 502);
        }

        return response()->json([], 200);
    }

    public function getDisable()
    {
        UserSpotifyToken::where('user_id', Auth::user()->id)->delete();

        return response()->json([], 200);
    }

    public function getAuthorizeUrl()
    {
        $state = Str::random(64);
        $url = SpotifyAPI::getAuthorizeUrl($state);

        Cache::put('spotify_oauth_state:'.Auth::id(), $state, now()->addMinutes(10));

        return $url;
    }

    public function getUserStats()
    {
        return response()->json($this->spotifyRepo->getImportStats(Auth::user()->id), 200);
    }

    public function getConnect(Request $request)
    {
        $input = $request->validate([
            'code' => 'required|string',
            'state' => 'required|string|size:64',
        ]);

        // Consume the pending state before exchanging any authorization code.
        $expectedState = Cache::pull('spotify_oauth_state:'.Auth::id());
        if (! is_string($expectedState) || ! hash_equals($expectedState, $input['state'])) {
            throw ValidationException::withMessages([
                'state' => ['Spotify authorization has expired or is invalid. Please connect again.'],
            ]);
        }

        $code = $input['code'];

        $session = SpotifyAPI::getSession();

        $session->requestAccessToken($code);

        $accessToken = $session->getAccessToken();
        $refreshToken = $session->getRefreshToken();

        $userId = Auth::user()->id;

        $token = UserSpotifyToken::where('user_id', $userId);
        if ($token) {
            // delete existing tokens
            $token = UserSpotifyToken::where('user_id', $userId)->delete();
        }

        $token = new UserSpotifyToken;
        $token->access_token = $accessToken;
        $token->refresh_token = $refreshToken;
        $token->user_id = $userId;
        $token->save();

        if ($token) {
            $success = true;
        } else {
            $success = false;
        }

        return response()->json([], 200);
    }

    public function disableAccess()
    {
        $userId = Auth::user()->id;

        if (! $userId) {
            return response()->json([
                'code' => 400,
                'message' => 'Bad user_id',
            ]);
        }

        UserSpotifyToken::where('user_id', $userId)->delete();

        return response()->json([
            'code' => 200,
            'message' => 'Successfully disabled access to Spotify',
        ]);
    }
}
