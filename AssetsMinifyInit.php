<?php
/**
 * @package Assets Minify
 */

use Assetic\Factory\AssetFactory;
use Assetic\AssetManager;
use Assetic\FilterManager;
use Assetic\Filter\JSMinFilter;
use Assetic\Filter\CssMinFilter;
use Assetic\Filter\CssRewriteFilter;
use Assetic\Filter\ScssphpFilter;
use Assetic\Filter\CompassFilter;
use Assetic\Filter\LessphpFilter;
use Assetic\Cache\FilesystemCache;

/**
 * Class that holds plugin's logic.
 */
class AssetsMinifyInit {

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

	protected $less         = array();
	protected $mTimesLess   = array();

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
		$this->css->getFilterManager()->set('CssRewrite', new CssRewriteFilter);
		$this->cssFilters []= $this->cssMin;

		//Define assets path to save asseticized files
		$uploadsDir = wp_upload_dir();
		$this->assetsUrl  = $uploadsDir['baseurl'] . '/am_assets/';
		$this->assetsPath = $uploadsDir['basedir'] . '/am_assets/';

		if ( !is_dir($this->assetsPath) )
			mkdir($this->assetsPath, 0777);
		elseif ( date('d') == 1 )
			$this->gc();

		$this->cache = new FilesystemCache( $this->assetsPath );

		//Detect all js and css added to wordpress and deny their inclusion
		add_action( 'wp_print_styles',  array( $this, 'extractStyles' ) );
		add_action( 'wp_print_scripts', array( $this, 'extractScripts' ) );

		//Inclusion of scripts in <head> and before </body>
		add_action( 'wp_head',   array( $this, 'headerServe' ) );
		add_action( 'wp_footer', array( $this, 'footerServe' ) );

	}

	public function gc() {
		foreach ( glob("{$this->assetsPath}*.*") as $filepath )
			if ( filemtime($filepath) <= time() - 2592000 ) //If the file is older than a month is removed
				unlink($filepath);
	}

	public function extractScripts() {
		global $wp_scripts;

		if ( empty($wp_scripts->queue) )
			return;

		foreach( $wp_scripts->queue as $handle ) {

			//Remove absolute part of the path if it's specified in the src
			$script = str_replace( "http://{$_SERVER['SERVER_NAME']}", "", $wp_scripts->registered[$handle]->src );

			//Don't manage other domains included scripts
			if ( strpos($script, "http") === 0 || strpos($script, "//") === 0 )
				continue;

			$where = 'footer';
			//Unfortunately not every WP plugin developer is a JS ninja
			//So... let's put it in the header.
			if ( empty($wp_scripts->registered[$handle]->extra) )
				$where = 'header';

			//Save the source filename for every script enqueued
			$filepath = getcwd() . $script;

			if ( !file_exists($filepath) )
				continue;

			$this->scripts[ $where ][ $handle ] = $filepath;
			$this->mTimes[ $where ][ $handle ]  = filemtime( $this->scripts[ $where ][ $handle ] );

			//Remove scripts from the queue so this plugin will be
			//responsible to include all the scripts
			$wp_scripts->dequeue( $handle );

		}

	}

	public function extractStyles() {
		global $wp_styles;

		if ( empty($wp_styles->queue) )
			return;

		foreach( $wp_styles->queue as $handle ) {

			//Remove absolute part of the path if it's specified in the src
			$style = str_replace( "http://{$_SERVER['SERVER_NAME']}", "", $wp_styles->registered[$handle]->src );

			//Don't manage other domains included stylesheets
			if ( strpos($style, "http") === 0 || strpos($style, "//") === 0 )
				continue;

			//Separation between css-frameworks stylesheets and .css stylesheets
			$ext = substr( $style, -5 );
			if ( in_array( $ext, array('.sass', '.scss') ) ) {
				$filepath = getcwd() . $style;

				if ( !file_exists($filepath) )
					continue;

				$this->sass[ $handle ]       = $filepath;
				$this->mTimesSass[ $handle ] = filemtime($this->sass[ $handle ]);
			} elseif ( $ext == '.less' ) {
				$filepath = getcwd() . $style;

				if ( !file_exists($filepath) )
					continue;

				$this->less[ $handle ]       = $filepath;
				$this->mTimesLess[ $handle ] = filemtime($this->less[ $handle ]);
			} else {
				$filepath = getcwd() . $style;

				if ( !file_exists($filepath) )
					continue;

				$this->styles[ $handle ]       = $filepath;
				$this->mTimesStyles[ $handle ] = filemtime($this->styles[ $handle ]);
			}

			//Remove css from the queue so this plugin will be
			//responsible to include all the stylesheets except other domains ones.
			$wp_styles->dequeue( $handle );

		}
	}

	public function headerServe() {

		//Compile css stylesheets
		$this->generateCss();

		//Compile sass stylesheets
		$this->generateSass();

		//Compile less stylesheets
		$this->generateLess();

		//Manage the stylesheets
		if ( !empty($this->styles) ) {
			$mtime = md5(implode('&', $this->mTimesStyles));

			//Save the asseticized stylesheets
			if ( !$this->cache->has( "head-{$mtime}.css" ) )
				$this->cache->set( "head-{$mtime}.css", str_replace('../', '/', $this->css->createAsset( $this->styles, $this->cssFilters )->dump() ) );

			//Print css inclusion in the page
			$this->dumpCss( "head-{$mtime}.css" );
		}

		//Manage the scripts to be printed in the header
		$this->headerServeScripts();

	}

	public function generateSass() {
		if ( empty($this->sass) )
			return false;

		$mtime = md5(implode('&', $this->mTimesSass));

		//If sass stylesheets have been updated -> compass compile
		if ( !$this->cache->has( "sass-{$mtime}.css" ) ) {

			if ( get_option('am_use_compass', 0) != 0 ) {
				//Define compass filter instance and sprite images paths
				$compassInstance = new CompassFilter( get_option('am_compass_path', '/usr/bin/compass') );
				$compassInstance->setImagesDir(get_theme_root() . "/" . get_template() . "/images");
				$compassInstance->setGeneratedImagesPath( $this->assetsPath );
				$compassInstance->setHttpGeneratedImagesPath( str_replace( getcwd(), '', $this->assetsPath ) );
				$this->css->getFilterManager()->set('Compass', $compassInstance);
				$filter = 'Compass';
			} else {
				$this->css->getFilterManager()->set('Scssphp', new ScssphpFilter);
				$filter = 'Scssphp';
			}

			//Save the asseticized stylesheets
			$this->cache->set( "sass-{$mtime}.css", $this->css->createAsset( $this->sass, array( $filter, 'CssRewrite' ) )->dump() );
		}

		//Add sass compiled stylesheet to normal css queue
		$this->styles['sass-am-generated']       = $this->assetsPath . "sass-{$mtime}.css";
		$this->mTimesStyles['sass-am-generated'] = filemtime($this->styles['sass-am-generated']);

	}

	public function generateLess() {
		if ( empty($this->less) )
			return false;

		$mtime = md5(implode('&', $this->mTimesLess));

		//If less stylesheets have been updated compile them
		if ( !$this->cache->has( "less-{$mtime}.css" )  ) {
			//Define compass filter instance and sprite images paths
			$this->css->getFilterManager()->set('Lessphp', new LessphpFilter);

			//Save the asseticized stylesheets
			$this->cache->set( "less-{$mtime}.css", $this->css->createAsset( $this->less, array( 'Lessphp', 'CssRewrite' ) )->dump() );
		}

		//Add less compiled stylesheet to normal css queue
		$this->styles['less-am-generated']       = $this->assetsPath . "less-{$mtime}.css";
		$this->mTimesStyles['less-am-generated'] = filemtime($this->styles['less-am-generated']);

	}

	public function generateCss() {
		if ( empty($this->styles) )
			return false;

		$mtime = md5(implode('&', $this->mTimesStyles));

		//If less stylesheets have been updated compile and save them 
		if ( !$this->cache->has( "styles-{$mtime}.css" ) )
			$this->cache->set( "styles-{$mtime}.css", $this->css->createAsset( $this->styles, array( 'CssRewrite' ) )->dump() );

		//Add less compiled stylesheet to normal css queue
		$this->styles       = array( 'styles-am-generated' => $this->assetsPath . "styles-{$mtime}.css");
		$this->mTimesStyles = array( 'styles-am-generated' => filemtime($this->styles['styles-am-generated']) );

	}

	public function headerServeScripts() {
		if ( empty($this->scripts['header']) )
			return false;

		$mtime = md5(implode('&', $this->mTimes['header']));

		//Save the asseticized header scripts
		if ( !$this->cache->has( "head-{$mtime}.js" ) )
			$this->cache->set( "head-{$mtime}.js", $this->js->createAsset( $this->scripts['header'], $this->jsFilters )->dump() );

		//Print <script> inclusion in the page
		$this->dumpJs( "head-{$mtime}.js", false );
	}

	public function footerServe() {
		if ( empty($this->scripts['footer']) )
			return false;

		$mtime = md5(implode('&', $this->mTimes['footer']));

		//Save the asseticized footer scripts
		if ( !$this->cache->has( "foot-{$mtime}.js" ) )
			$this->cache->set( "foot-{$mtime}.js", $this->js->createAsset( $this->scripts['footer'], $this->jsFilters )->dump() );

		//Print <script> inclusion in the page
		$this->dumpJs( "foot-{$mtime}.js" );
	}

	protected function dumpJs( $filename, $async = true ) {
		echo "<script type='text/javascript' src='" . $this->assetsUrl . $filename . "'" . ($async ? " async" : "") . "></script>";
	}

	protected function dumpCss( $filename ) {
		echo "<link href='" . $this->assetsUrl . $filename . "' media='screen, projection' rel='stylesheet' type='text/css'>";
	}
}

new AssetsMinifyInit;
