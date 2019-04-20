<?php

namespace App\Http\Middleware;

use View;
use Cache;
use Closure;


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
        if(@$userId) {
            $lastCheck = Cache::get($userId . "_activity_last_check", false);

            $count = $request->user()->getActivityFeedCount($lastCheck);

            View::share('activity_feed_count', $count);
        }

        return $next($request);
    }
}
