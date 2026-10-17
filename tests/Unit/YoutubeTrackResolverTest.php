<?php

namespace Tests\Unit;

use App\Services\Discovery\YoutubeTrackResolver;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\CreatesApplication;

class YoutubeTrackResolverTest extends TestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        config(['cache.default' => 'array', 'youtube.key' => 'test-key']);
        Cache::flush();
        DB::shouldReceive('table')->with('title_filters')->andReturnSelf();
        DB::shouldReceive('pluck')->with('value')->andReturn(collect());
    }

    private function track(string $id): object
    {
        return (object) ['id' => $id, 'name' => $id, 'artists' => [(object) ['name' => 'Artist']]];
    }

    private function response(string $id): Response
    {
        return new Response(200, [], json_encode(['items' => [[
            'id' => ['videoId' => $id],
            'snippet' => ['title' => 'Artist - '.$id, 'thumbnails' => ['high' => ['url' => 'https://example.test/thumb.jpg']]],
        ]]]));
    }

    public function test_reuses_raw_matches_but_generates_fresh_player_state(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([$this->response('a'), $this->response('b')]));
        $stack->push(Middleware::history($history));
        $resolver = new YoutubeTrackResolver(new Client(['handler' => $stack]));
        $first = $resolver->resolve([$this->track('a')]);
        $first[0]->collected = true;
        $first[0]->mediaId = 999;
        $second = $resolver->resolve([$this->track('a'), $this->track('b'), $this->track('a')]);

        $this->assertCount(2, $history);
        $this->assertSame(['a', 'b', 'a'], array_column($second, 'videoId'));
        $this->assertFalse($second[0]->collected);
        $this->assertSame(0, $second[0]->mediaId);
        $this->assertNotSame($first[0]->guid, $second[0]->guid);
        $this->assertNotSame($second[0]->guid, $second[2]->guid);
        parse_str($history[0]['request']->getUri()->getQuery(), $query);
        $this->assertSame('Artist - a', $query['q']);
        $this->assertSame('1', $query['maxResults']);
        $this->assertSame(10, $history[0]['options']['timeout']);
        $this->assertSame(3, $history[0]['options']['connect_timeout']);
    }

    public function test_empty_search_is_cached_briefly_and_expires(): void
    {
        $resolver = new YoutubeTrackResolver(new Client(['handler' => new MockHandler([
            new Response(200, [], '{"items":[]}'), $this->response('a'),
        ])]));
        $this->assertSame([], $resolver->resolve([$this->track('a')]));
        $this->assertSame([], $resolver->resolve([$this->track('a')]));
        $this->travel(6)->minutes();
        $this->assertSame('a', $resolver->resolve([$this->track('a')])[0]->videoId);
        $this->travelBack();
    }

    public function test_rate_limit_and_malformed_responses_are_not_cached(): void
    {
        foreach ([new Response(429), new Response(200, [], 'invalid json'), new Response(200, [], '{"items":[{}]}')] as $failure) {
            Cache::flush();
            $resolver = new YoutubeTrackResolver(new Client(['handler' => new MockHandler([$failure, $this->response('a')])]));
            try {
                $resolver->resolve([$this->track('a')]);
                $this->fail('Expected failure');
            } catch (\RuntimeException $e) {
                $this->assertStringNotContainsString('test-key', $e->getMessage());
            }
            $this->assertSame('a', $resolver->resolve([$this->track('a')])[0]->videoId);
        }
    }

    public function test_transport_failure_can_be_retried_without_exposing_credentials(): void
    {
        $resolver = new YoutubeTrackResolver(new Client(['handler' => new MockHandler([
            new ConnectException('timeout test-key', new Request('GET', 'https://example.test/?key=test-key')),
            $this->response('a'),
        ])]));
        try {
            $resolver->resolve([$this->track('a')]);
            $this->fail('Expected failure');
        } catch (\RuntimeException $e) {
            $this->assertStringNotContainsString('test-key', $e->getMessage());
        }
        $this->assertSame('a', $resolver->resolve([$this->track('a')])[0]->videoId);
    }

    public function test_pool_is_bounded_and_preserves_order_when_requests_finish_out_of_order(): void
    {
        $started = 0;
        $active = 0;
        $peak = 0;
        $promises = [];
        $handler = function ($request) use (&$started, &$active, &$peak, &$promises) {
            parse_str($request->getUri()->getQuery(), $query);
            $id = substr($query['q'], strlen('Artist - '));
            $started++;
            $active++;
            $peak = max($peak, $active);
            $promise = new Promise(function () use (&$promises, &$active) {
                // Resolve the newest request first to force completion out of order.
                foreach (array_reverse($promises, true) as $id => $pending) {
                    if ($pending->getState() === 'pending') {
                        $active--;
                        $pending->resolve($this->response((string) $id));
                    }
                }
            });
            $promises[$id] = $promise;

            return $promise;
        };
        $resolver = new YoutubeTrackResolver(new Client(['handler' => $handler]));
        $tracks = array_map(fn ($id) => $this->track((string) $id), range(1, 9));
        $videos = $resolver->resolve($tracks);

        $this->assertSame(9, $started);
        $this->assertSame(4, $peak);
        $this->assertSame(array_map('strval', range(1, 9)), array_column($videos, 'videoId'));
    }
}
