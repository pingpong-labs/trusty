<?php

namespace Pingpong\Trusty;

use Illuminate\Database\Eloquent\Model;
use Pingpong\Trusty\Traits\SlugableTrait;

class Role extends Model
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
        return $this->belongsToMany(config('trusty.model.permission'))->withTimestamps();
    }

    /**
     * @param $query
     * @param $search
     *
     * @return mixed
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereName($search)
                     ->orWhere('id', intval($search))
                     ->orWhere('slug', intval($search));
    }

    /**
     * @param $id
     */
    public function addPermission($id)
    {
        $this->permissions()->attach($id);
    }

    /**
     * @param $ids
     */
    public function removePermission($ids)
    {
        $ids = is_array($ids) ? $ids : func_get_args();

        $this->permissions()->detach($ids);
    }

    /**
     *
     */
    public function clearPermissions()
    {
        $this->removePermission($this->permissions->lists('id'));
    }

    /**
     * Relation to "User".
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(config('trusty.model.user'))->withTimestamps();
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function can($name)
    {
        foreach ($this->permissions as $permission) {
            if ($permission->name == $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handle dynamic method.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return bool
     */
    public function __call($method, $parameters = array())
    {
        if (starts_with($method, 'can') and $method != 'can') {
            return $this->can(snake_case(substr($method, 3)));
        } else {
            $query = $this->newQuery();

            return call_user_func_array(array($query, $method), $parameters);
        }
    }
}
