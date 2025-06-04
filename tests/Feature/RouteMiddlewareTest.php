<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteMiddlewareTest extends TestCase
{
    public function test_dashboard_route_has_auth_and_verified_middleware(): void
    {
        $route = Route::getRoutes()->getByName('dashboard');
        $this->assertNotNull($route, 'Route named dashboard not found');
        $middleware = $route->middleware();
        $this->assertContains('auth', $middleware);
        $this->assertContains('verified', $middleware);
    }
}
