<?php
/**
 * Bootstrap class for AssetsMinify plugin.
 * It's the only entry point of this plugin.
 */
class AssetsMinify {
	protected static $instance;

	/**
	 * Singleton class manager
	 */
	public static function bootstrap() {
		if ( !isset(self::$instance) ) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}

	/**
	 * Constructor. Identify if is admin session or not
	 */
	protected function __construct() {
		if ( !is_admin() )
			return new AssetsMinify\Init;

		add_action( 'plugins_loaded', array( $this, 'loadAdmin' ) );
	}

	/**
	 * Initialize the admin panel
	 */
	public function loadAdmin() {
		new AssetsMinify\Admin;
	}
}
