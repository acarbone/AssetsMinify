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
use Assetic\Asset\StringAsset;

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

	/**
	 * Constructor of the class
	 */
	public function __construct() {

		//Init assetic's object to manage js minify
		$this->js = new AssetFactory( getcwd() );
		$this->js->setAssetManager( new AssetManager );
		$this->js->setFilterManager( new FilterManager );

		//Init assetic's object to manage css minify
		$this->css = new AssetFactory( getcwd() );
		$this->css->setAssetManager( new AssetManager );
		$this->css->setFilterManager( new FilterManager );

		//Defines filter for js minify
		$this->js->getFilterManager()->set($this->jsMin, new JSMinFilter);
		$this->jsFilters []= $this->jsMin;

		//Defines filter for css minify
		$this->css->getFilterManager()->set($this->cssMin, new CssMinFilter);
		$this->css->getFilterManager()->set('CssRewrite', new CssRewriteFilter);
		$this->cssFilters []= $this->cssMin;

		//Define assets path to save asseticized files
		$uploadsDir = wp_upload_dir();
		$this->assetsUrl  = $uploadsDir['baseurl'] . '/am_assets/';
		$this->assetsPath = $uploadsDir['basedir'] . '/am_assets/';

		if ( !is_dir($this->assetsPath) ) //Creates the AM cache dir
			mkdir($this->assetsPath, 0777);
		elseif ( get_option('am_last_gc', 0) <= time() - 864000 ) //Every 10 days the garbage collector is called
			$this->gc();

		//Manager for Filesystem management
		$this->cache = new FilesystemCache( $this->assetsPath );

		//Detects all js and css added to WordPress and removes their inclusion
		add_action( 'wp_print_styles',  array( $this, 'extractStyles' ) );
		add_action( 'wp_print_scripts', array( $this, 'extractScripts' ) );

		//Inclusion of scripts in <head> and before </body>
		add_action( 'wp_head',   array( $this, 'headerServe' ) );
		add_action( 'wp_footer', array( $this, 'footerServe' ) );

	}

	/**
	 * Garbage collector for 10 days old files
	 */
	public function gc() {
		update_option( 'am_last_gc', time() );
		foreach ( glob("{$this->assetsPath}*.*") as $filepath )
			if ( filemtime($filepath) <= time() - 864000 ) //If the file is older than 10 days then is removed
				unlink($filepath);
	}

	/**
	 * Cleans the path of the site from the filepath
	 */
	public function cleanPath( $filepath ) {
		return str_replace( get_site_url(), "", $filepath );
	}

	/**
	 * Takes all the scripts enqueued to the theme and removes them from the queue
	 */
	public function extractScripts() {
		global $wp_scripts;

		if ( empty($wp_scripts->queue) )
			return;

		// Trigger dependency resolution
		$wp_scripts->all_deps($wp_scripts->queue);

		foreach( $wp_scripts->to_do as $key => $handle ) {
			//Removes absolute part of the path if it's specified in the src
			$script = $this->cleanPath($wp_scripts->registered[$handle]->src);

			$script = str_replace( "/wp-includes/", str_replace( getcwd(), '', ABSPATH ) . "wp-includes/", $script );

			//Doesn't manage other domains included scripts
			if ( strpos($script, "http") === 0 || strpos($script, "//") === 0 )
				continue;

			$where = 'footer';
			//Unfortunately not every WP plugin developer is a JS ninja
			//So... let's put it in the header.
			if ( empty($wp_scripts->registered[$handle]->extra) )
				$where = 'header';

			//Saves the source filename for every script enqueued
			$filepath = getcwd() . $script;

			if ( !file_exists($filepath) )
				continue;

			$this->scripts[ $where ][ $handle ] = $filepath;
			$this->mTimes[ $where ][ $handle ]  = filemtime( $this->scripts[ $where ][ $handle ] );

			//Removes scripts from the queue so this plugin will be
			//responsible to include all the scripts except other domains ones.
			$wp_scripts->dequeue( $handle );

			//Move the handle to the done array.
			$wp_scripts->done[] = $handle;
			unset($wp_scripts->to_do[$key]);
		}
	}

	/**
	 * Takes all the stylesheets enqueued to the theme and removes them from the queue
	 */
	public function extractStyles() {
		global $wp_styles;

		if ( empty($wp_styles->queue) )
			return;

		// Trigger dependency resolution
		$wp_styles->all_deps($wp_styles->queue);

		foreach( $wp_styles->to_do as $key => $handle ) {
			//Removes absolute part of the path if it's specified in the src
			$style = $this->cleanPath($wp_styles->registered[$handle]->src);

			//Doesn't manage other domains included stylesheets
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

			//Removes css from the queue so this plugin will be
			//responsible to include all the stylesheets except other domains ones.
			$wp_styles->dequeue( $handle );

			//Move the handle to the done array.
			$wp_styles->done[] = $handle;
			unset($wp_styles->to_do[$key]);
		}
	}

	/**
	 * Returns header's inclusion for CSS and JS (if provided)
	 */
	public function headerServe() {

		//Compiles CSS stylesheets
		$this->generateCss();

		//Compiles SASS stylesheets
		$this->generateSass();

		//Compiles LESS stylesheets
		$this->generateLess();

		//Manages the stylesheets
		if ( !empty($this->styles) ) {
			$mtime = md5(implode('&', $this->mTimesStyles));

			//Saves the asseticized stylesheets
			if ( !$this->cache->has( "head-{$mtime}.css" ) )
				$this->cache->set( "head-{$mtime}.css", str_replace('../', '/', $this->css->createAsset( $this->styles, $this->cssFilters )->dump() ) );

			//Prints css inclusion in the page
			$this->dumpCss( "head-{$mtime}.css" );
		}

		//Manages the scripts to be printed in the header
		$this->headerServeScripts();

	}

	/**
	 * Takes all the SASS stylesheets and manages their queue to asseticize them
	 */
	public function generateSass() {
		if ( empty($this->sass) )
			return false;

		$mtime = md5(implode('&', $this->mTimesSass));

		//If SASS stylesheets have been updated -> compass compile
		if ( !$this->cache->has( "sass-{$mtime}.css" ) ) {

			if ( get_option('am_use_compass', 0) != 0 ) {
				//Defines compass filter instance and sprite images paths
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

			//Saves the asseticized stylesheets
			$this->cache->set( "sass-{$mtime}.css", $this->css->createAsset( $this->sass, array( $filter, 'CssRewrite' ) )->dump() );
		}

		//Adds SASS compiled stylesheet to normal css queue
		$this->styles['sass-am-generated']       = $this->assetsPath . "sass-{$mtime}.css";
		$this->mTimesStyles['sass-am-generated'] = filemtime($this->styles['sass-am-generated']);

	}

	/**
	 * Takes all the LESS stylesheets and manages their queue to asseticize them
	 */
	public function generateLess() {
		if ( empty($this->less) )
			return false;

		$mtime = md5(implode('&', $this->mTimesLess));

		//If LESS stylesheets have been updated compile them
		if ( !$this->cache->has( "less-{$mtime}.css" )  ) {
			//Defines compass filter instance and sprite images paths
			$this->css->getFilterManager()->set('Lessphp', new LessphpFilter);

			//Saves the asseticized stylesheets
			$this->cache->set( "less-{$mtime}.css", $this->css->createAsset( $this->less, array( 'Lessphp', 'CssRewrite' ) )->dump() );
		}

		//Adds LESS compiled stylesheet to normal css queue
		$this->styles['less-am-generated']       = $this->assetsPath . "less-{$mtime}.css";
		$this->mTimesStyles['less-am-generated'] = filemtime($this->styles['less-am-generated']);

	}

	/**
	 * Takes all the CSS stylesheets and manages their queue to asseticize them
	 */
	public function generateCss() {
		if ( empty($this->styles) )
			return false;

		$mtime = md5(implode('&', $this->mTimesStyles));

		//If CSS stylesheets have been updated compile and save them 
		if ( !$this->cache->has( "styles-{$mtime}.css" ) )
			$this->cache->set( "styles-{$mtime}.css", $this->css->createAsset( $this->styles, array( 'CssRewrite' ) )->dump() );

		//Adds CSS compiled stylesheet to normal css queue
		$this->styles       = array( 'styles-am-generated' => $this->assetsPath . "styles-{$mtime}.css");
		$this->mTimesStyles = array( 'styles-am-generated' => filemtime($this->styles['styles-am-generated']) );

	}

	/**
	 * Returns header's inclusion for JS (if provided)
	 */
	public function headerServeScripts() {
		if ( empty($this->scripts['header']) )
			return false;

		$mtime = md5(implode('&', $this->mTimes['header']));

		//Saves the asseticized header scripts
		if ( !$this->cache->has( "head-{$mtime}.js" ) )
			$this->cache->set( "head-{$mtime}.js", $this->js->createAsset( $this->scripts['header'], $this->jsFilters )->dump() );

		//Prints <script> inclusion in the page
		$this->dumpScriptData( 'header' );
		$this->dumpJs( "head-{$mtime}.js", false );
	}

	/**
	 * Returns footer's inclusion for JS (if provided)
	 */
	public function footerServe() {
		if ( empty($this->scripts['footer']) )
			return false;

		$mtime = md5(implode('&', $this->mTimes['footer']));

		//Saves the asseticized footer scripts
		if ( !$this->cache->has( "foot-{$mtime}.js" ) )
			$this->cache->set( "foot-{$mtime}.js", $this->js->createAsset( $this->scripts['footer'], $this->jsFilters )->dump() );

		//Prints <script> inclusion in the page
		$this->dumpScriptData( 'footer' );
		$this->dumpJs( "foot-{$mtime}.js" );
	}

	/**
	 * Combines the script data from all minified scripts
	 */
	protected function buildScriptData( $where ) {
		global $wp_scripts;
		$data = '';

		if ( empty($this->scripts[$where] ) )
			return '';

		foreach ($this->scripts[$where] as $handle => $filepath) {
			$data .= $wp_scripts->print_extra_script( $handle, false );
		}

		$asset = new StringAsset( $data, array(new JSMinFilter) );

		return $asset->dump();
	}

	/**
	 * Prints <script> tag to include the JS
	 */
	protected function dumpJs( $filename, $async = true ) {
		echo "<script type='text/javascript' src='" . $this->assetsUrl . $filename . "'" . ($async ? " async" : "") . "></script>";
	}

	/**
	 * Prints <link> tag to include the CSS
	 */
	protected function dumpCss( $filename ) {
		echo "<link href='" . $this->assetsUrl . $filename . "' media='screen, projection' rel='stylesheet' type='text/css'>";
	}

	/**
	 * Prints <script> tags with addtional script data and i10n
	 */
	protected function dumpScriptData( $where ) {
		$data = $this->buildScriptData( $where );

		if (empty($data))
			return false;

		echo "<script type='text/javascript'>\n"; // CDATA and type='text/javascript' is not needed for HTML 5
		echo "/* <![CDATA[ */\n";
		echo "$data\n";
		echo "/* ]]> */\n";
		echo "</script>\n";
	}
}

new AssetsMinifyInit;