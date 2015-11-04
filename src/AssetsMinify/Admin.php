<?php
namespace AssetsMinify;

use AssetsMinify\Cache;

/**
 * Admin's page manager.
 * Prints out every field managed from AssetsMinify's admin page
 *
 * @author Alessandro Carbone <ale.carbo@gmail.com>
 */
class Admin {

	/**
	 * Admin options provided
	 */
	protected $options = array(
		'am_use_compass',
		'am_compass_path',
		'am_sass_path',
		'am_coffeescript_path',
		'am_async_flag',
		'am_compress_styles',
		'am_compress_scripts',
		'am_files_to_exclude',
		'am_log',
		'am_dev_mode',
	);

	/**
	 * Constructor
	 */
	public function __construct() {
		// Cache manager
		$this->cache = new Cache;

		add_action('admin_init', array( $this, 'options') );
		add_action('admin_menu', array( $this, 'menu') );

		if ( isset($_GET['empty_cache']) ) {
			$this->cache->flush();
			wp_redirect( admin_url( "options-general.php?page=assets-minify" ) );
		}
	}

	/**
	 * Initalizes the plugin's admin menu
	 */
	public function menu() {
		add_options_page('AssetsMinify', 'AssetsMinify', 'administrator', 'assets-minify', array( $this, 'settings') );
	}

	/**
	 * Outputs the tpl provided
	 *
	 * @param string $tplFile The template to output
	 */
	protected function tpl( $tplFile ) {
		include plugin_dir_path( dirname(dirname(__FILE__)) ) . 'templates/' . $tplFile;
	}

	/**
	 * Registers plugin's options
	 */
	public function options() {
		foreach ( $this->options as $opt ) {
			register_setting('am_options_group', $opt);
		}
		return $this->options;
	}

	/**
	 * Defines plugin's settings
	 */
	public function settings() {
		$this->tpl( "settings.phtml" );
	}
}
