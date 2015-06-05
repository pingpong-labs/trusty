<?php

namespace Pingpong\Trusty;

use Illuminate\Support\ServiceProvider;

class TrustyServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the package.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/src/migrations/' => base_path('/database/migrations'),
            __DIR__.'/src/config/config.php' => config_path('trusty.php'),
        ]);
        $this->mergeConfigFrom(__DIR__.'/src/config/config.php', 'trusty');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app['trusty'] = $this->app->share(function ($app) {
            $auth = $app['auth']->driver();

            return new Trusty($auth, $app['router']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('trusty');
    }
}
