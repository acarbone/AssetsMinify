<?php
/**
 * @package Assets Minify
 */

/**
 * Class that holds admin functionalities for AssetsMinify
 */
class AssetsMinifyAdmin {

	public function __construct() {
		add_action('admin_init', array( $this, 'options') );
		add_action('admin_menu', array( $this, 'menu') );
	}

	/**
	* Initalizes the plugin's admin menu
	*/
	public function menu() {
		add_options_page('AssetsMinify', 'AssetsMinify', 'administrator', 'assets-minify', array( $this, 'settings') );
	}

	protected function tpl( $tplFile ) {
		include plugin_dir_path( __FILE__ ) . 'templates/' . $tplFile;
	}

	public function options() {
		register_setting('am_options_group', 'am_use_compass');
		register_setting('am_options_group', 'am_compass_path');
	}

	/**
	* Defines plugin's settings
	*/
	public function settings() {
		$this->tpl( "settings.phtml" );
	}
}
add_action( 'plugins_loaded', function() {
	new AssetsMinifyAdmin;	
});
