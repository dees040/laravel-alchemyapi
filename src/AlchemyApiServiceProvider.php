<?php

namespace dees040\AlchemyAPI;

use Illuminate\Support\ServiceProvider;

class AlchemyApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('dees040\AlchemyAPI\AlchemyAPI', function ($app) {
            return new \dees040\AlchemyAPI\AlchemyAPI();
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
