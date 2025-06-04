<?php

return [
    'cleanup_threshold_days' => env('LISTING_CLEANUP_THRESHOLD_DAYS', 60),
    'statistics_cache_ttl' => env('LISTING_STATISTICS_CACHE_TTL', 86400),
];
