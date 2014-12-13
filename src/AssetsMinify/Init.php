<?php
namespace AssetsMinify;

use AssetsMinify\Assets\Css;
use AssetsMinify\Assets\Js;

/**
 * Class that holds plugin's logic.
 */
class Init {

	public $js, 
		   $css;

	protected $exclusions;

	/**
	 * Constructor of the class
	 */
	public function __construct() {
		$this->cache = new Cache;

		$this->js = new Js;
		$this->js->setCache( $this->cache );
		$this->css = new Css;
		$this->css->setCache( $this->cache );

		$this->exclusions = preg_split('/[ ]*,[ ]*/', trim(get_option('am_files_to_exclude')));

		//Detects all js and css added to WordPress and removes their inclusion
		if( get_option('am_compress_styles', 1) )
			add_action( 'wp_print_styles',  array( $this->css, 'extract' ) );
		if( get_option('am_compress_scripts', 1) )
			add_action( 'wp_print_scripts', array( $this->js, 'extract' ) );

		//Inclusion of scripts in <head> and before </body>
		add_action( 'wp_head',   array( $this, 'header' ) );
		add_action( 'wp_footer', array( $this, 'footer' ) );
	}

	/**
	 * Checks if the file is within the list of "to exclude" resources
	 *
	 * @param string $path The file path
	 * @return boolean Whether the file is to exclude or not
	 */
	protected function isFileExcluded( $path ) {
		$filename = explode('/', $path);
		if ( in_array($filename[ count($filename) - 1 ], $this->exclusions) )
			return true;

		return false;
	}

	/**
	 * Returns header's inclusion for CSS and JS (if provided)
	 */
	public function header() {
		$this->css->generate();
		$this->js->generate('header');
	}

	/**
	 * Returns footer's inclusion for JS (if provided)
	 */
	public function footer() {
		$this->js->generate('footer');
	}
}