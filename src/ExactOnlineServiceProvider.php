<?php

namespace PolarisDC\Exact\ExactOnlineConnector;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ExactOnlineServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerRoutes();
        $this->configurePublishing();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/exact-online-connector.php', 'exact-online');

        $this->app->singleton(ExactOnlineService::class, fn () => new ExactOnlineService());
        $this->app->singleton(Connection::class, fn ($app) => $app->make(ExactOnlineService::class)->getConnection());

        //$this->app->singleton();
    }

    /**
     * Configure publishing.
     */
    protected function configurePublishing(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/exact-online-connector.php' => config_path('exact-online.php'),
        ], ['exact-online-connector', 'exact-online-connector:config']);

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], ['exact-online-connector', 'exact-online-connector:migrations']);
    }

    /**
     * Register the routes.
     */
    protected function registerRoutes()
    {
        Route::group([
            'prefix'     => config('exact-online.route_prefix', 'exact-online'),
            'middleware' => config('exact-online.route_middleware', ['web', 'auth']),
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }
}