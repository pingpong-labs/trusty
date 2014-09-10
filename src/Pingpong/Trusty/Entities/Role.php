<?php namespace Pingpong\Trusty\Entities;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Pingpong\Trusty\Traits\SlugableTrait;

class Role extends \Eloquent
{
	use SlugableTrait;
	
	/**
	 * Fillable property.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'slug', 'description'];

	/**
	 * Relation to "Permission".
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function permissions()
	{
		return $this->belongsToMany(__NAMESPACE__ . '\\Permission')->withTimestamps();
	}

	/**
	 * Relation to "User".
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function users()
	{
		return $this->belongsToMany(Config::get('auth.model'))->withTimestamps();
	}

	/**
	 * Check whether the user role can perform the given permission.
	 *
	 * @param  string  $permission
	 * @return boolean
	 */
	public function can($permission)
	{
		return in_array($permission, array_fetch($this->permissions->toArray(), 'slug'));
	}

	/**
	 * Handle dynamic method.
	 *
	 * @param  string  $method  
	 * @param  array   $parameters  
	 * @return boolean
	 */
	public function __call($method, $parameters = array())
	{
		if(starts_with($method, 'can') and $method != 'can')
		{
			return $this->can(snake_case(substr($method, 3)));
		}
		else
		{
			$query = $this->newQuery();

			return call_user_func_array(array($query, $method), $parameters);
		}
	}
}