<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class MetricsService
{
    public function record(string $path, float $durationMs, int $memoryUsage, bool $isError): void
    {
        Log::channel('metrics')->info('request_metrics', [
            'path' => $path,
            'duration_ms' => $durationMs,
            'memory_usage_bytes' => $memoryUsage,
            'error' => $isError,
        ]);
    }
}
