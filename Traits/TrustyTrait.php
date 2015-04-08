<?php namespace Pingpong\Trusty\Traits;

use Illuminate\Support\Collection;
use Pingpong\Trusty\Role;

trait TrustyTrait {

    /**
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany(config('trusty.model.role'))->withTimestamps();
    }

    /**
     * @param $idOrName
     */
    public function addRole($idOrName)
    {
        $ids = is_array($idOrName) ? $idOrName : func_get_args();

        foreach ($ids as $search)
        {
            $role = Role::search($idOrName)->firstOrFail();

            $this->roles()->attach($role->id);
        }
    }

    /**
     * @param $idOrName
     */
    public function removeRole($idOrName)
    {
        $ids = is_array($idOrName) ? $idOrName : func_get_args();

        foreach ($ids as $search)
        {
            $role = Role::search($search)->firstOrFail();

            $this->roles()->detach($role->id);
        }
    }

    /**
     *
     */
    public function detachRoles()
    {
        $this->removeRole($this->roles->lists('id'));
    }

    /**
     * @param $name
     * @return bool
     */
    public function is($name)
    {
        foreach ($this->roles as $role)
        {
            if ($role->name == $name || $role->slug == $name)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $name
     * @return bool
     */
    public function can($name)
    {
        foreach ($this->roles as $role)
        {
            foreach ($role->permissions as $permission)
            {
                if ($permission->name == $name || $permission->slug == $name)
                {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return Collection
     */
    public function getPermissionsAttribute()
    {
        $permissions = new Collection;

        foreach ($this->roles as $role)
        {
            foreach ($role->permissions as $permission)
            {
                $permissions->push($permission);
            }
        }

        return $permissions;
    }

    /**
     * Handle dynamic method.
     *
     * @param  string $method
     * @param  array $parameters
     * @return boolean
     */
    public function __call($method, $parameters = array())
    {
        if (starts_with($method, 'is') and $method != 'is')
        {
            return $this->is(snake_case(substr($method, 2)));
        }
        elseif (starts_with($method, 'can') and $method != 'can')
        {
            return $this->can(snake_case(substr($method, 3)));
        }
        elseif (in_array($method, ['increment', 'decrement']))
        {
            return call_user_func_array([$this, $method], $parameters);
        }
        else
        {
            $query = $this->newQuery();

            return call_user_func_array([$query, $method], $parameters);
        }
    }

}
