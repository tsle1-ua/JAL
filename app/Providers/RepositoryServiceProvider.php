<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\ListingRepositoryInterface;
use App\Repositories\EloquentListingRepository;
use App\Repositories\EventRepositoryInterface;
use App\Repositories\EloquentEventRepository;
use App\Repositories\LeisureZoneRepositoryInterface;
use App\Repositories\EloquentLeisureZoneRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Binding de repositorios
        $this->app->bind(ListingRepositoryInterface::class, EloquentListingRepository::class);
        $this->app->bind(EventRepositoryInterface::class, EloquentEventRepository::class);
        $this->app->bind(LeisureZoneRepositoryInterface::class, EloquentLeisureZoneRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}