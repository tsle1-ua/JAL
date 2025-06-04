<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_custom_404_page_is_rendered(): void
    {
        $response = $this->get('/non-existent-page');

        $response->assertStatus(404);
        $response->assertSee('Página no encontrada');
    }

    public function test_custom_500_page_is_rendered(): void
    {
        Route::get('/force-error', function () {
            abort(500);
        });

        config(['app.debug' => false]);

        $response = $this->get('/force-error');

        $response->assertStatus(500);
        $response->assertSee('Error del servidor');
    }
}
