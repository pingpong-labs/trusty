<?php

use Mockery as m;
use Pingpong\Trusty\Trusty;
use Pingpong\Trusty\TrustyServiceProvider;

class TrustyTest extends PHPUnit_Framework_TestCase {

	protected $app;
	
	protected $provider;

	protected $auth;

	protected $router;

	protected $trusty;

	public function setUp()
	{
		$this->auth = m::mock('Illuminate\Auth\Guard');
		$this->router = m::mock('Illuminate\Routing\Router');
		$this->app = m::mock('Illuminate\Container\Container');
		$this->trusty = new Trusty($this->auth, $this->router);
		$this->provider = new TrustyServiceProvider($this->app);
	}

	public function test_initialize()
	{
		$this->assertInstanceOf('Pingpong\Trusty\Trusty', $this->trusty);	
	}

	public function test_trusty_when()
	{
		$this->router->shouldReceive('when')->once();
		$this->trusty->when('/admin/users/*', 'manage_users');
	}

	public function test_trusty_when_with_multiple_request()
	{
		$this->router->shouldReceive('when')->times(2);
		$this->trusty->when(['/admin/posts/*', '/admin/posts'], 'manage_posts');
	}

	public function test_registers_all_permissions()
	{
		$permissions = ['manage_posts', 'manage_users'];
		$this->auth->shouldReceive('check')->once()->andReturn(true);
		$this->router->shouldReceive('filter')->times(2);
		$this->trusty->registerPermissions($permissions);	
	}

	public function test_trusty_forbidden()
	{
		$this->setExpectedException('Pingpong\Trusty\Exceptions\ForbiddenException');
		$this->trusty->forbidden();
	}

	public function test_service_provider()
	{
		$this->assertInstanceOf('Pingpong\Trusty\TrustyServiceProvider', $this->provider);
		$this->app->shouldReceive('share');
		$this->app->shouldReceive('offsetSet');
		$this->provider->register();	
		$this->app->shouldReceive('offsetGet')->once()->with('trusty')->andReturn($this->trusty);
		$this->assertInstanceOf('Pingpong\Trusty\Trusty', $this->app['trusty']);
	}
	
}