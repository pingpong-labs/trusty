<?php

namespace Pingpong\Trusty;

use Illuminate\Auth\Guard;
use Illuminate\Routing\Router;
use Pingpong\Trusty\Exceptions\PermissionDeniedException;

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
     * @param string|array $request
     * @param string       $permission
     * @param string       $httpVerbs
     */
    public function when($request, $permission, $httpVerbs = null)
    {
        foreach ((array) $request as $uri) {
            $this->router->when($uri, $permission, $httpVerbs ?: $this->httpVerbs);
        }
    }

    /**
     * Register the permissions.
     *
     * @param array|null $permissions
     */
    public function registerPermissions(array $permissions = null)
    {
        $permissions = $permissions ?: Permission::lists('slug');

        foreach ($permissions as $permission) {
            $this->router->filter($permission, function () use ($permission) {
                if (!$this->auth->user()->can($permission)) {
                    $this->forbidden();
                }
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

    /**
     * Filter the specified request by the given permissions.
     *
     * @param string|array $permissions
     */
    public function filterByPermission($permissions)
    {
        $permissions = is_array($permissions) ? $permissions : func_get_args();

        foreach ($permissions as $permission) {
            if (!$this->auth->user()->can($permission)) {
                throw new PermissionDeniedException("You don't have permission to \"{$permission}\".");
            }
        }
    }

    /**
     * Filter the specified request by the given roles.
     *
     * @param string|array $roles
     */
    public function filterByRole($roles)
    {
        $roles = is_array($roles) ? $roles : func_get_args();

        foreach ($roles as $role) {
            if (!$this->auth->user()->is($role)) {
                throw new PermissionDeniedException("You aren't a \"{$role}\".");
            }
        }
    }
}
