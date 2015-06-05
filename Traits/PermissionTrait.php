<?php

namespace Pingpong\Trusty\Traits;

use Pingpong\Trusty\Facades\Trusty;

trait PermissionTrait
{
    /**
     * Filter the specified request by the given permissions.
     *
     * @param string|array $permissions
     */
    public function beforePermission($permissions)
    {
        $permissions = is_array($permissions) ? $permissions : func_get_args();

        $this->beforeFilter(function () use ($permissions) {
            Trusty::filterByPermission($permissions);
        });
    }

    /**
     * Filter the specified request by the given roles.
     *
     * @param string|array $roles
     */
    public function beforeRole($roles)
    {
        $roles = is_array($roles) ? $roles : func_get_args();

        $this->beforeFilter(function () use ($roles) {
            Trusty::filterByRole($roles);
        });
    }
}
