<?php
/**
 * @package Assets Minify
 */

namespace AssetsMinify;

use Assetic\Factory\AssetFactory;
use Assetic\AssetManager;
use Assetic\FilterManager;
use Assetic\Filter\JSMinFilter;
use Assetic\Filter\CssMinFilter;
use Assetic\Filter\CompassFilter;
use Assetic\Cache\FilesystemCache;

/**
 * Class that holds plugin's logic.
 */
class Init {

	public $js;
	public $css;

	protected $assetsPath;
	protected $assetsUrl;

	protected $jsFilters    = array();
	protected $cssFilters   = array();

	protected $styles       = array();
	protected $mTimesStyles = array();

	protected $sass         = array();
	protected $mTimesSass   = array();

	protected $scripts      = array( 'header' => array(), 'footer' => array() );
	protected $mTimes       = array( 'header' => array(), 'footer' => array() );

	protected $jsMin        = 'JSMin';
	protected $cssMin       = 'CssMin';

	public function __construct() {

		//Init assetic's object to manage js minify
		$this->js = new AssetFactory( getcwd() );
		$this->js->setAssetManager( new AssetManager );
		$this->js->setFilterManager( new FilterManager );

		//Init assetic's object to manage css minify
		$this->css = new AssetFactory( getcwd() );
		$this->css->setAssetManager( new AssetManager );
		$this->css->setFilterManager( new FilterManager );

		//Define filter for js minify
		$this->js->getFilterManager()->set($this->jsMin, new JSMinFilter);
		$this->jsFilters []= $this->jsMin;

		//Define filter for css minify
		$this->css->getFilterManager()->set($this->cssMin, new CssMinFilter);
		$this->cssFilters []= $this->cssMin;

		//Define assets path to save asseticized files
		$uploadsDir = wp_upload_dir();
		$this->assetsUrl  = $uploadsDir['baseurl'] . '/am_assets/';
		$this->assetsPath = $uploadsDir['basedir'] . '/am_assets/';
		if ( !is_dir($this->assetsPath) )
			mkdir($this->assetsPath, 0777);

		$this->cache = new FilesystemCache( $this->assetsPath );

		//Detect all js and css added to wordpress and deny their inclusion
		add_action( 'wp_print_styles',  array( $this, 'extractStyles' ) );
		add_action( 'wp_print_scripts', array( $this, 'extractScripts' ) );

		//Inclusion of scripts in <head> and before </body>
		add_action( 'wp_head',   array( $this, 'headerServe' ) );
		add_action( 'wp_footer', array( $this, 'footerServe' ) );

	}

	public function extractScripts() {
		global $wp_scripts;

		foreach( $wp_scripts->queue as $handle ) {

			$where = 'footer';
			//Unfortunately not every WP plugin developer is a JS ninja
			//So... let's put it in the header.
			if ( empty($wp_scripts->registered[$handle]->extra) )
				$where = 'header';

			//Save the source filename for every script enqueued
			$this->scripts[ $where ][ $handle ] = getcwd() . str_replace( "http://{$_SERVER['SERVER_NAME']}", "", $wp_scripts->registered[$handle]->src );

			$this->mTimes[ $where ][ $handle ] = filemtime( $this->scripts[ $where ][ $handle ] );

			//Remove scripts from the queue so this plugin will be
			//responsible to include all the scripts
			$wp_scripts->dequeue( $handle );

		}

	}

	public function extractStyles() {
		global $wp_styles;

		foreach( $wp_styles->queue as $handle ) {

			//Remove absolute part of the path if it's specified in the src
			$style = str_replace( "http://{$_SERVER['SERVER_NAME']}", "", $wp_styles->registered[$handle]->src );

			//Don't manage other domains included stylesheets
			if ( strpos($style, "http") === 0 )
				continue;

			$ext = substr( $style, -5 );
			if ( in_array( $ext, array('.sass', '.scss') ) ) {
				$this->sass[ $handle ] = getcwd() . $style;
				$this->mTimesSass[ $handle ] = filemtime($this->sass[ $handle ]);
			} else {
				$this->styles[ $handle ] = getcwd() . $style;
				$this->mTimesStyles[ $handle ] = filemtime($this->styles[ $handle ]);
			}

			//Remove css from the queue so this plugin will be
			//responsible to include all the stylesheets except other domains ones.
			$wp_styles->dequeue( $handle );

		}
	}

	public function headerServe() {

		//Manage the scripts to be printed in the header
		$this->headerServeScripts();

		//Compile sass stylesheets
		$this->generateSass();

		//Manage the stylesheets
		if ( empty($this->styles) )
			return false;

		$mtime = md5(implode('&', $this->mTimesStyles));

		if ( !$this->cache->has( "head.css" ) || get_option('as_minify_head_css_mtime') != $mtime ) {
			update_option( 'as_minify_head_css_mtime', $mtime );

			//Save the asseticized stylesheets
			$this->cache->set( "head.css", $this->css->createAsset( $this->styles, $this->cssFilters )->dump() );
		}

		//Print css inclusion in the page
		$this->dumpCss( 'head.css' );

	}

	public function generateSass() {
		if ( empty($this->sass) )
			return false;

		$mtime = md5(implode('&', $this->mTimesSass));

		if ( !$this->cache->has( "sass.css" ) || get_option('as_minify_head_sass_mtime') != $mtime ) {
			update_option( 'as_minify_head_sass_mtime', $mtime );

			$compassInstance = new CompassFilter;
			$compassInstance->setImagesDir(get_theme_root() . "/" . get_template() . "/images");
			$this->css->getFilterManager()->set('Compass', $compassInstance);

			//Save the asseticized stylesheets
			$this->cache->set( "sass.css", $this->css->createAsset( $this->sass, array( 'Compass' ) )->dump() );
		}

		$this->styles['sass-am-generated'] = $this->assetsPath . "sass.css";
		$this->mTimesStyles['sass-am-generated'] = filemtime($this->styles['sass-am-generated']);

	}

	public function headerServeScripts() {
		if ( empty($this->scripts['header']) )
			return false;

		$mtime = md5(implode('&', $this->mTimes['header']));

		if ( !$this->cache->has( "head.js" ) || get_option('as_minify_head_mtime') != $mtime ) {
			update_option( 'as_minify_head_mtime', $mtime );

			//Save the asseticized header scripts
			$this->cache->set( "head.js", $this->js->createAsset( $this->scripts['header'], $this->jsFilters )->dump() );
		}

		//Print <script> inclusion in the page
		$this->dumpJs( 'head.js' );
	}

	public function footerServe() {
		if ( empty($this->scripts['footer']) )
			return false;

		$mtime = md5(implode('&', $this->mTimes['footer']));

		if ( !$this->cache->has( "foot.js" ) || get_option('as_minify_foot_mtime') != $mtime ) {
			update_option( 'as_minify_foot_mtime', $mtime );

			//Save the asseticized footer scripts
			$this->cache->set( "foot.js", $this->js->createAsset( $this->scripts['footer'], $this->jsFilters )->dump() );
		}

		//Print <script> inclusion in the page
		$this->dumpJs( 'foot.js' );
	}

	protected function dumpJs( $filename ) {
		echo "<script type='text/javascript' src='" . $this->assetsUrl . $filename . "'></script>";
	}

	protected function dumpCss( $filename ) {
		echo "<link href='" . $this->assetsUrl . $filename . "' media='screen, projection' rel='stylesheet' type='text/css'>";
	}
}

new Init;