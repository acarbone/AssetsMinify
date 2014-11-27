<?php
/**
 * Bootstrap class for AssetsMinify plugin.
 * It's the only entry point of this plugin.
 */
class AssetsMinify extends AssetsMinify\Pattern\Singleton {
	/**
	 * Constructor. Identify if admin session or not
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
