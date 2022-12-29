<?php

namespace App\Providers;

use App\Extensions\CacheEloquentProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }

    public function register()
    {
        $this->app['auth']->provider(
            'cached-user-driver',
            function ($app, $config) {
                return new CacheEloquentProvider(
                    $this->app['hash'],
                    $config['model']
                );
            }
        );
    }
}
