<?php

namespace App\Http\Middleware;

use App\Services\MetricsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MetricsMiddleware
{
    protected MetricsService $metricsService;

    public function __construct(MetricsService $metricsService)
    {
        $this->metricsService = $metricsService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);
        $startMemory = memory_get_usage();
        $isError = false;
        try {
            /** @var Response $response */
            $response = $next($request);
            if ($response->getStatusCode() >= 500) {
                $isError = true;
            }
        } catch (\Throwable $e) {
            $isError = true;
            throw $e;
        } finally {
            $duration = (microtime(true) - $start) * 1000;
            $memoryUsage = memory_get_usage() - $startMemory;
            $this->metricsService->record($request->path(), $duration, $memoryUsage, $isError);
        }

        return $response;
    }
}
