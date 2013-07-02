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
			$uploadsDir = wp_upload_dir();
			$filesList = glob($uploadsDir['basedir'] . '/am_assets/' . "*.*");
			if ( $filesList !== false )
				array_map('unlink', $filesList);
			wp_redirect( admin_url( "options-general.php?page=assets-minify" ) );
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
		register_setting('am_options_group', 'am_coffeescript_path');
		register_setting('am_options_group', 'am_compress_styles');
		register_setting('am_options_group', 'am_compress_scripts');
		register_setting('am_options_group', 'am_files_to_exclude');
	}

	/**
	* Defines plugin's settings
	*/
	public function settings() {
		$this->tpl( "settings.phtml" );
	}
}
function amPluginsLoaded() {
	return new AssetsMinifyAdmin;	
}
add_action( 'plugins_loaded', 'amPluginsLoaded' );