<?php

namespace Liou2021\Gcs;

use Illuminate\Support\ServiceProvider;

class GCSServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $source = realpath($raw = __DIR__ . '/../config/gcs.php') ?: $raw;
        $this->publishes([
            $source => config_path('gcs.php'),
        ]);
    }

    public function register()
    {
        $configPath = __DIR__ . '/../config/gcs.php';
        $this->mergeConfigFrom($configPath, 'gcs');

        $this->app->singleton('GCS', function ($app) {
            return new GCS();
        });
    }
}
