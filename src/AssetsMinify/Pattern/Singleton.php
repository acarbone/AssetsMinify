<?php
namespace AssetsMinify\Pattern;

/**
 * Singleton Design Pattern.
 * Provides an abstract structure for a single instance class. 
 *
 * @abstract
 * @author Alessandro Carbone <ale.carbo@gmail.com>
 */
abstract class Singleton {
	protected function __construct() {}

	/**
	 * Method to get the single object instantiated for the class
	 *
	 * @final
	 * @static
	 */
	final public static function getInstance() {
		static $instances = array();

		$calledClass = get_called_class();

		if ( !isset($instances[$calledClass]) ) {
			$instances[$calledClass] = new $calledClass(func_get_args());
		}

		return $instances[$calledClass];
	}

	final private function __clone() {}
}