<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AuthJwtServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Interfaces\AuthInterface', 'App\Repositories\AuthRepository');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
