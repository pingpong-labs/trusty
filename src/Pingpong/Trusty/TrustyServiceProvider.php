<?php namespace Pingpong\Trusty;

use Illuminate\Support\ServiceProvider;

class TrustyServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	public function boot()
	{
		$this->package('pingpong/trusty');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['pingpong.trusty'] = $this->app->share(function($app)
		{
			return new Trusty;	
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('pingpong.trusty');
	}

}
