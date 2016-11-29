<?php

namespace dees040\AlchemyApi;

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
        $this->app->bind('dees040\AlchemyApi\AlchemyApi', function ($app) {
            return new \dees040\AlchemyApi\AlchemyApi();
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
