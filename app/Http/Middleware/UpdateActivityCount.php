<?php

namespace App\Http\Middleware;

use Cache;
use Closure;
use View;

class UpdateActivityCount
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $userId = @$request->user()->id;
        if (@$userId) {
            $lastCheck = Cache::get($userId.'_activity_last_check', false);

            $count = $request->user()->getActivityFeedCount($lastCheck);

            View::share('discovered_items_count', $request->user()->getRecentDiscoveredItemsCount());
            View::share('activity_feed_count', $count);
        }

        return $next($request);
    }
}
