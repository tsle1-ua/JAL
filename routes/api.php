<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ListingController as ApiListingController;
use App\Http\Controllers\API\EventController as ApiEventController;

Route::middleware('throttle:5,1')->post('/login', [AuthController::class, 'login']);
Route::get('/login/{provider}/redirect', [AuthController::class, 'redirectToProvider']);
Route::get('/login/{provider}/callback', [AuthController::class, 'handleProviderCallback']);

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('listings', ApiListingController::class)->except(['index', 'show']);
    Route::apiResource('events', ApiEventController::class)->except(['index', 'show']);
});

Route::apiResource('listings', ApiListingController::class)->only(['index', 'show']);
Route::apiResource('events', ApiEventController::class)->only(['index', 'show']);
