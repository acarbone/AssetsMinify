<?php
/**
 * Bootstrap class for AssetsMinify plugin.
 * It's the only entry point of this plugin.
 * A singleton class.
 *
 * @author Alessandro Carbone <ale.carbo@gmail.com>
 */

class AssetsMinify extends AssetsMinify\Pattern\Singleton {

	/**
	 * @var \AssetsMinify\Init
	 */
	private $initObject = null;

	/**
	 * Constructor
	 */
	protected function __construct() {
		if ( !is_admin() ) {
			$this->initObject = new AssetsMinify\Init;
			return;
		}

		add_action( 'plugins_loaded', array( $this, 'loadAdmin' ) );
	}

	/**
	 * Initialize the admin panel
	 */
	public function loadAdmin() {
		new AssetsMinify\Admin;
	}

	/**
	 * Provides access to the central Init object.
	 */
	public function getInitObject() {
		return $this->initObject;
	}
}