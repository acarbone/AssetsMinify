<?php
namespace AssetsMinify\Pattern;

abstract class Container {

	protected static $stack = array();

	final public static function set($name, $value) {
		self::$stack[ $name ] = new $value( get_called_class() );
		return true;
	}

	final public function get($name) {
		return self::$stack[ $name ];
	}
}