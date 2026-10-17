<?php

return [
    // Keep upstream load bounded. Set to 1 to disable parallel searches.
    'youtube_concurrency' => env('DISCOVERY_YOUTUBE_CONCURRENCY', 4),
];
