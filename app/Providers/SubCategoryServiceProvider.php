<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SubCategoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'App\Interfaces\SubCategoryInterface',
            'App\Repositories\SubCategoryRepository'
        );
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
