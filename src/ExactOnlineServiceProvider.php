<?php

namespace PolarisDC\Laravel\ExactOnlineConnector;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use PolarisDC\ExactOnlineConnector\Configs\AppInformation;
use PolarisDC\ExactOnlineConnector\ExactOnlineConnector;
use PolarisDC\ExactOnlineConnector\Interfaces\TokenVaultInterface;

class ExactOnlineServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerRoutes();
        $this->configurePublishing();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/exact-online-connector.php', 'exact-online');

        $this->app->bind(TokenVaultInterface::class, static function ($app) {
            $tokenVault = resolve(\PolarisDC\Laravel\ExactOnlineConnector\TokenVault::class);
            $tokenVault->setClientId($app['config']['exact-online']['client_id']);
            return $tokenVault;
        });

        // todo not sure how we can use this for multi tenant setups
        $this->app->singleton(ExactOnlineConnector::class, static function ($app) {

            $appInformation = new AppInformation(
                $app['config']['exact-online']['client_id'],
                $app['config']['exact-online']['client_secret'],
                $app['config']['exact-online']['client_webhook_secret'],
                $app['config']['exact-online']['redirect_url'],
                $app['config']['exact-online']['base_url'],
                $app['config']['exact-online']['division'],
                $app['config']['exact-online']['language_code'],
            );

            $tokenVault = $app->make(TokenVaultInterface::class);

            if ($app['config']['exact-online']['token_storage']['use_filesystem'] && method_exists($tokenVault, 'setStoragePath')) {
                $filesystemConfig = $app['config']['exact-online']['token_storage']['filesystem'];
                $tokenVault->setStoragePath(Storage::disk($filesystemConfig['disk'])->path($filesystemConfig['path']));
            }

            $exactOnlineConnector = new ExactOnlineConnector($appInformation, $tokenVault);
            $exactOnlineConnector->setLogService($app->make('log'));

            return $exactOnlineConnector;
        });
    }

    /**
     * Configure publishing.
     */
    protected function configurePublishing(): void
    {
        if (! $this->app->runningInConsole()) {
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
    protected function registerRoutes(): void
    {
        Route::group([
            'prefix' => config('exact-online.routing.prefix'),
            'middleware' => config('exact-online.routing.middleware'),
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }
}