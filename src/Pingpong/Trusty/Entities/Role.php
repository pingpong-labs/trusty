<?php namespace Pingpong\Trusty\Entities;

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
}