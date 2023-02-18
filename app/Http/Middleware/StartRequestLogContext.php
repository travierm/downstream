<?php

namespace App\Http\Middleware;

use App\Global\RequestLogContext;
use Closure;
use Illuminate\Http\Request;

class StartRequestLogContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        RequestLogContext::startRequestLog([
            'user_id' => $request->user()->id,
            'path' => $request->path(),
            'method' => $request->method(),
            'controller' => $request->route()->getAction('controller')
        ]);

        RequestLogContext::info('new request');

        return $next($request);
    }
}
