<?php

namespace SmartPanel\Mjml\Providers;

use Illuminate\Support\ServiceProvider;

class MjmlServiceProviders extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/config/package.php' => config_path('package.php'),
            ], 'config');
        }
    }
}
