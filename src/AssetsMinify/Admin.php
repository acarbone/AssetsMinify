<?php
namespace AssetsMinify;

class Admin {

	protected $options = array(
		'am_use_compass',
		'am_compass_path',
		'am_coffeescript_path',
		'am_async_flag',
		'am_compress_styles',
		'am_compress_scripts',
		'am_files_to_exclude',
	);

	public function __construct() {
		add_action('admin_init', array( $this, 'options') );
		add_action('admin_menu', array( $this, 'menu') );

		if ( isset($_GET['empty_cache']) ) {
			$this->emptyCache();
			wp_redirect( admin_url( "options-general.php?page=assets-minify" ) );
		}
	}

	public function emptyCache() {
		$uploadsDir = wp_upload_dir();
		$filesList = glob($uploadsDir['basedir'] . '/am_assets/' . "*.*");
		if ( $filesList !== false ) {
			array_map('unlink', $filesList);
		}
	}

	/**
	* Initalizes the plugin's admin menu
	*/
	public function menu() {
		add_options_page('AssetsMinify', 'AssetsMinify', 'administrator', 'assets-minify', array( $this, 'settings') );
	}

	protected function tpl( $tplFile ) {
		include plugin_dir_path( dirname(dirname(__FILE__)) ) . 'templates/' . $tplFile;
	}

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