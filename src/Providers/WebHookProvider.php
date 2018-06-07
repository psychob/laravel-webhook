<?php

namespace PsychoB\WebHook\Providers;

use Illuminate\Support\ServiceProvider;

class WebHookProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        if (config('webhook.register_routes', false)) {
            $this->loadRoutesFrom(__DIR__ . '/../../routes/app.php');
        }

        $this->publishes([
            __DIR__ . '/../../config/webhook.php' => config_path('webhook.php'),
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}