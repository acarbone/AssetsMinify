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

		if ( isset($_GET['empty_cache']) ) {
			update_option( 'as_minify_head_css_mtime', 'toUpdate' );
			update_option( 'as_minify_head_sass_mtime', 'toUpdate' );
			update_option( 'as_minify_head_less_mtime', 'toUpdate' );
			update_option( 'as_minify_head_mtime', 'toUpdate' );
			update_option( 'as_minify_foot_mtime', 'toUpdate' );
			wp_redirect( admin_url( str_replace(array('/wp-admin/', '&empty_cache'), '', $_SERVER['REQUEST_URI']) ) );
		}
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
function amPluginsLoaded() {
	new AssetsMinifyAdmin;	
}
add_action( 'plugins_loaded', 'amPluginsLoaded' );
spl_autoload_register('amAutoloader');