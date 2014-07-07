<?php namespace Pingpong\Trusty\Entities;

use Pingpong\Trusty\Traits\SlugableTrait;

class Permission extends \Eloquent
{
	use SlugableTrait;

	/**
	 * Fillable property.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'slug', 'description'];

	/**
	 * Relation to "Role".
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function roles()
	{
		return $this->belongsToMany(__NAMESPACE__ . '\\Role')->withTimestamps();
	}
}