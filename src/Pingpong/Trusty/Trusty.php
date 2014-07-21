<?php namespace Pingpong\Trusty;

use Pingpong\Trusty\Entities\Permission;
use Auth, View, Request, Closure, Route;

class Trusty
{
	/**
	 * The avaliable HTTP Verbs.
	 * 
	 * @var array
	 */
	protected $httpVerbs = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

	/**
	 * The name of view for showing forbiden page when the user is not have a specified permission.
	 * 	
	 * @var string
	 */
	protected $view = '403';

	/**
	 * Register new filter for the specified request.
	 * 
	 * @param  string|array $request    
	 * @param  string       $permission 
	 * @return void           
	 */
	public function when($request, $permission)
	{
		foreach ((array) $request as $uri)
		{
			Route::when($uri, $permission, $this->httpVerbs);
		}
	}

	/**
	 * Set new view.
	 * 
	 * @param string $view
	 */
	public function setView($view)
	{
		$this->view = $view;
	}

	/**
	 * Register the permissions.
	 * 
	 * @return void 
	 */
	public function registerPermissions()
	{
		if( ! Auth::check()) return $this->forbidden();

		foreach(Permission::lists('slug') as $permission)
		{
		    Route::filter($permission, function() use ($permission)
		    {
		        if( ! Auth::user()->can($permission)) return $this->forbidden();
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
        return View::make($this->view);			
	}
}