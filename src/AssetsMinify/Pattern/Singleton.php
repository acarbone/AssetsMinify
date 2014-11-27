<?php
namespace AssetsMinify\Pattern;

abstract class Singleton {
	protected function __construct() {}

	final public static function getInstance() {
		static $instances = array();

		$calledClass = get_called_class();

		if ( !isset($instances[$calledClass]) ) {
			$instances[$calledClass] = new $calledClass();
		}

		return $instances[$calledClass];
	}

	final private function __clone() {}
}