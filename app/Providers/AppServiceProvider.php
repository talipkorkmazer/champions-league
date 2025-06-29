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
        $this->configureSecureUrls();
    }

    protected function configureSecureUrls()
    {
        // Determine if HTTPS should be enforced
        $enforceHttps = $this->app->environment(['production'])
            && !$this->app->runningUnitTests();

        // Force HTTPS for all generated URLs
        URL::forceHttps($enforceHttps);

        // Ensure proper server variable is set
        if ($enforceHttps) {
            $this->app['request']->server->set('HTTPS', 'on');
        }

        // Set up global middleware for security headers
        if ($enforceHttps) {
            $this->app['router']->pushMiddlewareToGroup('web', function ($request, $next) {
                $response = $next($request);

                return $response->withHeaders([
                    'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
                    'Content-Security-Policy' => "upgrade-insecure-requests",
                    'X-Content-Type-Options' => 'nosniff'
                ]);
            });
        }
    }
}
