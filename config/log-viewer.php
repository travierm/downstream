<?php

use Opcodes\LogViewer\Http\Middleware\AuthorizeLogViewer;

return [
    'enabled' => env('LOG_VIEWER_ENABLED', true),
    'route_path' => 'log-viewer',

    // The SPA uses bearer tokens on a separate origin. Browser authentication
    // lets admins use their existing account without passing tokens in URLs.
    'middleware' => ['web', 'auth.basic', AuthorizeLogViewer::class],
    'api_middleware' => ['web', 'auth.basic', AuthorizeLogViewer::class],

    // Only index application logs; container stdout/stderr stay available too.
    'include_files' => ['*.log', '**/*.log'],
    'back_to_system_url' => env('APP_LINK_URL', env('APP_URL')),
    'cache_driver' => env('LOG_VIEWER_CACHE_DRIVER', 'file'),
];
