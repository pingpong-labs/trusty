<?php

namespace Pingpong\Trusty\Facades;

use Illuminate\Support\Facades\Facade;

class Trusty extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'trusty';
    }
}
