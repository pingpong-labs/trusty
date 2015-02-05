<?php namespace Pingpong\Trusty\Facades;

use Illuminate\Support\Facades\Facade;

class Trusty extends Facade {

	/**
	 * @return string
     */
	protected static function getFacadeAccessor()
	{
		return 'trusty';
	}

}