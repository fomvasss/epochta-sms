<?php

namespace Fomvasss\Epochta;

use Fomvasss\Epochta\Services\Epochta;
use Illuminate\Support\ServiceProvider;

class EpochtaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => app()->basePath() . '/config/epochta_sms.php'
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Epochta', function($app) {
            return new Epochta();
        });
    }



    private function registerEpochta()
    {
        //
    }


}
