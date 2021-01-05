<?php

namespace PolarisDC\Exact\ExactOnlineConnector;

use Illuminate\Support\ServiceProvider;

class ExactOnlineServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->configurePublishing();
    }

    public function register()
    {
        $this->app->singleton('exact-online-connection', fn($app) => new ExactOnlineConnection());
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
            __DIR__ . '/../config/exact-online.php' => config_path('exact-online.php'),
        ], ['exact-online-connector', 'exact-online-connector:config']);

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], ['exact-online-connector', 'exact-online-connector:migrations']);
    }
}