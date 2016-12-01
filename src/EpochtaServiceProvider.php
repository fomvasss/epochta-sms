<?php

namespace Fomvasss\Epochta;

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

        //include __DIR__.'/routes.php';

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
       // $this->registerEpochta();
    }



    private function registerEpochta()
    {
        $this->app->bind('laratrust', function ($app) {
            return new Epochta($app);
        });

        $this->app->alias('epochta', 'Epochta\Epochta');
    }


}
