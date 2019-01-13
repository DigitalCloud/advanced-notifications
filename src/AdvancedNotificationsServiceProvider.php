<?php

namespace DigitalCloud\AdvancedNotifications;

use DigitalCloud\AdvancedNotifications\ChannelManager;
use Illuminate\Notifications\ChannelManager as IlluminateChannelManager;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AdvancedNotificationsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([
            __DIR__.'/Migrations' => database_path('migrations')
        ], 'migrations');

        $this->loadMigrationsFrom(__DIR__.'/Migrations');

        $this->publishes([
            $this->configPath() => config_path('notifications.php'),
        ], 'notifications-config');


//        AdvancedNotifications::notificationsIn(app_path('Notifications'));
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(IlluminateChannelManager::class, function ($app) {
            return new ChannelManager($app);
        });
    }

    protected function configPath()
    {
        return __DIR__.'/../config/notifications.php';
    }
}
