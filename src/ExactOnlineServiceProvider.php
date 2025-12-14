<?php

namespace TimothyDC\ExactOnline\LaravelClient;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use TimothyDC\ExactOnline\BaseClient\ClientConfiguration;
use TimothyDC\ExactOnline\BaseClient\ExactOnlineClient;
use TimothyDC\ExactOnline\BaseClient\Interfaces\TokenVaultInterface;

class ExactOnlineServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerRoutes();
        $this->configurePublishing();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/exact-online-client.php', 'exact-online');

        $this->app->bind(TokenVaultInterface::class, static function ($app) {
            $tokenVault = resolve(TokenVault::class);
            $tokenVault->setClientId($app->make('config')['exact-online']['client_id']);
            return $tokenVault;
        });

        // todo not sure how we can use this for multi tenant setups
        $this->app->singleton(ExactOnlineClient::class, static function ($app) {

            $config = $app->make('config');

            $appInformation = new ClientConfiguration(
                $config['exact-online']['client_id'],
                $config['exact-online']['client_secret'],
                $config['exact-online']['client_webhook_secret'],
                $config['exact-online']['redirect_url'],
                $config['exact-online']['base_url'],
                $config['exact-online']['division'],
                $config['exact-online']['language_code'],
            );

            $tokenVault = $app->make(TokenVaultInterface::class);

            if ($config['exact-online']['token_storage']['use_filesystem'] && method_exists($tokenVault, 'setStoragePath')) {
                $filesystemConfig = $config['exact-online']['token_storage']['filesystem'];
                $tokenVault->setStoragePath(Storage::disk($filesystemConfig['disk'])->path($filesystemConfig['path']));
            }

            $exactOnlineClient = new ExactOnlineClient($appInformation, $tokenVault);
            $exactOnlineClient->setLogger($app->make('log'));

            return $exactOnlineClient;
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
            __DIR__ . '/../config/exact-online-client.php' => config_path('exact-online.php'),
        ], ['exact-online-client', 'exact-online-client:config']);

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], ['exact-online-client', 'exact-online-client:migrations']);
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
