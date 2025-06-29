<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Services\Interfaces\StandingServiceInterface;
use App\Services\Interfaces\FixtureServiceInterface;
use App\Services\Interfaces\PredictionServiceInterface;
use App\Services\Interfaces\SimulationServiceInterface;
use App\Services\StandingService;
use App\Services\FixtureService;
use App\Services\PredictionService;
use App\Services\SimulationService;

/**
 * Application service provider for registering services and bootstrapping the application
 */
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
        $this->app->bind(SimulationServiceInterface::class, SimulationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
