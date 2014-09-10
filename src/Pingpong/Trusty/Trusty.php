<?php namespace Pingpong\Trusty;

use Illuminate\Auth\Guard;
use Illuminate\Routing\Router;
use Pingpong\Trusty\Entities\Permission;

class Trusty
{
	/**
	 * The avaliable HTTP Verbs.
	 * 
	 * @var array
	 */
	protected $httpVerbs = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

	/**
	 * The constructor.
	 * 
	 * @param Guard  $auth   
	 * @param Router $router 
	 */
	public function __construct(Guard $auth, Router $router)
	{
		$this->auth = $auth;
		$this->router = $router;
	}

	/**
	 * Register new filter for the specified request.
	 * 
	 * @param  string|array $request    
	 * @param  string       $permission 
	 * @return self           
	 */
	public function when($request, $permission)
	{
		foreach ((array) $request as $uri)
		{
			$this->router->when($uri, $permission, $this->httpVerbs);
		}
	}

	/**
	 * Register the permissions.
	 *
	 * @param  array|null $permissions 
	 * @return void 
	 */
	public function registerPermissions(array $permissions = null)
	{
		if( ! $this->auth->check()) $this->forbidden();

		$permissions = $permissions ?: Permission::lists('slug');

		foreach($permissions as $permission)
		{
		    $this->router->filter($permission, function() use ($permission)
		    {
		        if( ! $this->auth->user()->can($permission)) $this->forbidden();
		    });
		}
	}

	/**
	 * Show forbidden page.
	 * 
	 * @return mixed 
	 */
	public function forbidden()
	{
		throw new Exceptions\ForbiddenException("Sorry, you don't have permission to access this page.");
	}

}