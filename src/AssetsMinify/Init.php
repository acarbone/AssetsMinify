<?php
namespace AssetsMinify;

use AssetsMinify\Assets\Css;
use AssetsMinify\Assets\Js;

/**
 * Init is the entry point for frontend assets management in WordPress using AssetsMinify.
 * It's the manager responsible for every object's instance.
 *
 * @author Alessandro Carbone <ale.carbo@gmail.com>
 */
class Init {

	public $js, 
		   $css;

	protected $exclusions;

	/**
	 * Constructor
	 */
	public function __construct() {
		// Cache manager
		$this->cache = new Cache;

		if ( get_option('am_dev_mode', 0) )
			$this->cache->flush();

		// Assets managers for Js and Css
		$this->js = new Js($this);
		$this->css = new Css($this);

		// Log manager
		$this->log = \AssetsMinify\Log::getInstance($this->cache);

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
	 * Checks if a file is within the list of resources to exclude
	 *
	 * @param string $path The file path
	 * @return boolean Whether the file is to exclude or not
	 */
	public function isFileExcluded( $path ) {
		$filename = explode('/', $path);
		if ( in_array($filename[ count($filename) - 1 ], $this->exclusions) )
			return true;

		return false;
	}

	/**
	 * Returns header's inclusion for CSS and JS
	 */
	public function header() {
		$this->css->generate();
		$this->js->generate('header');
	}

	/**
	 * Returns footer's inclusion for JS
	 */
	public function footer() {
		$this->js->generate('footer');
		if ( $this->cache->isUpdated() )
			Log::getInstance()->dumpStorage();
	}
}