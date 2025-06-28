<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Interfaces\StandingServiceInterface;
use App\Services\Interfaces\FixtureServiceInterface;
use App\Services\Interfaces\PredictionServiceInterface;
use App\Services\StandingService;
use App\Services\FixtureService;
use App\Services\PredictionService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(StandingServiceInterface::class, StandingService::class);
        $this->app->bind(FixtureServiceInterface::class, FixtureService::class);
        $this->app->bind(PredictionServiceInterface::class, PredictionService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
