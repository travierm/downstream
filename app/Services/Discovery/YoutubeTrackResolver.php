<?php

namespace App\Services\Discovery;

use App\MediaType\Transformer\SpotifyTrackTransformer;
use App\MediaType\YoutubeVideo;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Pool;
use Illuminate\Support\Facades\Cache;

/** Resolves recommendations without caching player identifiers or collection state. */
class YoutubeTrackResolver
{
    private ClientInterface $http;

    public function __construct(?ClientInterface $http = null)
    {
        $this->http = $http ?? new Client;
    }

    /** @return array<YoutubeVideo> */
    public function resolve(array $tracks): array
    {
        $keys = [];
        $rawResults = [];
        $pending = [];
        foreach ($tracks as $track) {
            $data = SpotifyTrackTransformer::transform($track);
            $key = 'discovery:youtube:v1:'.hash('sha256', $data['spotify_id'].'|'.$data['title']);
            $keys[] = $key;
            if (array_key_exists($key, $rawResults) || isset($pending[$key])) {
                continue;
            }
            $cached = Cache::get($key);
            if ($cached !== null) {
                $rawResults[$key] = $cached;
            } else {
                $pending[$key] = $data['title'];
            }
        }

        $failed = false;
        $requests = function () use ($pending, &$failed) {
            foreach ($pending as $key => $query) {
                if ($failed) {
                    return;
                }
                yield $key => function () use ($query) {
                    return $this->http->requestAsync('GET', 'https://www.googleapis.com/youtube/v3/search', [
                        'query' => [
                            'key' => config('youtube.key'),
                            'q' => $query,
                            'type' => 'video',
                            'part' => 'id,snippet',
                            'maxResults' => 1,
                        ],
                        'connect_timeout' => 3,
                        'timeout' => 10,
                        // Inspect failures ourselves; never surface URLs containing API keys.
                        'http_errors' => false,
                    ]);
                };
            }
        };

        $pool = new Pool($this->http, $requests(), [
            'concurrency' => max(1, min(4, (int) config('discovery.youtube_concurrency', 4))),
            'fulfilled' => function ($response, $key) use (&$rawResults, &$failed) {
                $body = json_decode((string) $response->getBody());
                if ($response->getStatusCode() !== 200 || ! isset($body->items) || ! is_array($body->items)) {
                    $failed = true;

                    return;
                }
                $item = $body->items[0] ?? null;
                if ($item !== null && (! isset($item->id->videoId) || ! isset($item->snippet->title))) {
                    $failed = true;

                    return;
                }
                // [] represents a successful search with no match, not a provider error.
                $rawResults[$key] = $item ?? [];
                Cache::put($key, $rawResults[$key], $item ? now()->addDay() : now()->addMinutes(5));
            },
            'rejected' => function () use (&$failed) {
                $failed = true;
            },
        ]);
        $pool->promise()->wait();

        if ($failed) {
            throw new \RuntimeException('YouTube discovery is temporarily unavailable. Please try again.');
        }

        $videos = [];
        foreach ($keys as $key) {
            if ($rawResults[$key] !== []) {
                $videos[] = YoutubeVideo::createFromSearchResult($rawResults[$key]);
            }
        }

        return $videos;
    }
}
