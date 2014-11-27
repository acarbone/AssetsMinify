<?php
namespace AssetsMinify;

use AssetsMinify\Assets\Css;
use AssetsMinify\Assets\Factory;
use AssetsMinify\Assets\Js;


use Assetic\Filter\JSMinFilter;
use Assetic\Filter\ScssphpFilter;
use Assetic\Filter\CoffeeScriptFilter;
use Assetic\Filter\CompassFilter;
use Assetic\Filter\LessphpFilter;
use Assetic\Asset\StringAsset;

use Minify_CSSmin;

/**
 * Class that holds plugin's logic.
 */
class Init {

	public $js;
	public $css;

	protected $exclusions;

	protected $assetsUrl;

	protected $styles       = array();
	protected $mTimesStyles = array();

	protected $sass         = array();
	protected $mTimesSass   = array();

	protected $less         = array();
	protected $mTimesLess   = array();

	protected $scripts      = array( 'header' => array(), 'footer' => array() );
	protected $mTimes       = array( 'header' => array(), 'footer' => array() );

	protected $coffee       = array( 'header' => array(), 'footer' => array() );
	protected $mTimesCoffee = array( 'header' => array(), 'footer' => array() );

	protected $cssMin       = '';

	/**
	 * Constructor of the class
	 */
	public function __construct() {
		$this->cache = new Cache;

		$this->js = new Js;
		$this->css = new Css;

		$this->exclusions = preg_split('/[ ]*,[ ]*/', trim(get_option('am_files_to_exclude')));

		//Detects all js and css added to WordPress and removes their inclusion
		if( get_option('am_compress_styles', 1) )
			add_action( 'wp_print_styles',  array( $this->css, 'extract' ) );
		if( get_option('am_compress_scripts', 1) )
			add_action( 'wp_print_scripts', array( $this->js, 'extract' ) );

		//Inclusion of scripts in <head> and before </body>
		add_action( 'wp_head',   array( $this, 'headerServe' ) );
		add_action( 'wp_footer', array( $this, 'footerServe' ) );
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
			if ( !$this->cache->fs->has( "head-{$mtime}.css" ) ) {
				$cssDump = str_replace('../', '/', $this->css->createAsset( $this->styles, $this->css->getFilters() )->dump() );
				$cssDump = str_replace( 'url(/wp-', 'url(' . site_url() . '/wp-', $cssDump );
				$cssDump = str_replace( 'url("/wp-', 'url("' . site_url() . '/wp-', $cssDump );
				$cssDump = str_replace( "url('/wp-", "url('" . site_url() . "/wp-", $cssDump );
				$this->cache->fs->set( "head-{$mtime}.css", $cssDump );
			}

			//Prints css inclusion in the page
			$this->dumpCss( "head-{$mtime}.css" );
		}

		//Manages the scripts from CoffeeScript to be printed in the header
		$this->generateCoffee('header');

		//Manages the scripts to be printed in the header
		$this->headerServeScripts();

	}

	/**
	 * Takes all the SASS stylesheets and manages their queue to asseticize them
	 */
	public function generateSass() {
		if ( empty($this->sass) )
			return false;

		$mtime = md5(implode('&', $this->mTimesSass) . implode('&', $this->sass));

		//If SASS stylesheets have been updated -> compass compile
		if ( !$this->cache->fs->has( "sass-{$mtime}.css" ) ) {

			if ( get_option('am_use_compass', 0) != 0 ) {
				//Defines compass filter instance and sprite images paths
				$compassInstance = new CompassFilter( get_option('am_compass_path', '/usr/bin/compass') );
				$compassInstance->setImagesDir(get_theme_root() . "/" . get_template() . "/images");
				$compassInstance->setGeneratedImagesPath( $this->assetsPath );
				$compassInstance->setHttpGeneratedImagesPath( site_url() . str_replace( getcwd(), '', $this->assetsPath ) );
				$this->css->getFilterManager()->set('Compass', $compassInstance);
				$filter = 'Compass';
			} else {
				$this->css->getFilterManager()->set('Scssphp', new ScssphpFilter);
				$filter = 'Scssphp';
			}

			//Saves the asseticized stylesheets
			$this->cache->fs->set( "sass-{$mtime}.css", $this->css->createAsset( $this->sass, array( $filter, 'CssRewrite' ) )->dump() );
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

		$mtime = md5(implode('&', $this->mTimesLess) . implode('&', $this->less));

		//If LESS stylesheets have been updated compile them
		if ( !$this->cache->fs->has( "less-{$mtime}.css" )  ) {
			//Defines compass filter instance and sprite images paths
			$this->css->getFilterManager()->set('Lessphp', new LessphpFilter);

			//Saves the asseticized stylesheets
			$this->cache->fs->set( "less-{$mtime}.css", $this->css->createAsset( $this->less, array( 'Lessphp', 'CssRewrite' ) )->dump() );
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

		$mtime = md5(implode('&', $this->mTimesStyles) . implode('&', $this->styles));

		//If CSS stylesheets have been updated compile and save them 
		if ( !$this->cache->fs->has( "styles-{$mtime}.css" ) )
			$this->cache->fs->set( "styles-{$mtime}.css", $this->css->createAsset( $this->styles, array( 'CssRewrite' ) )->dump() );

		//Adds CSS compiled stylesheet to normal css queue
		$this->styles       = array( 'styles-am-generated' => $this->assetsPath . "styles-{$mtime}.css");
		$this->mTimesStyles = array( 'styles-am-generated' => filemtime($this->styles['styles-am-generated']) );
	}

	/**
	 * Returns coffeescript inclusions for JS (if provided)
	 */
	public function generateCoffee( $type ) {
		if ( empty($this->coffee[$type]) )
			return false;

		$mtime = md5(implode('&', $this->mTimesCoffee[$type]) . implode('&', $this->coffee[$type]) );

		//Saves the asseticized header scripts
		if ( !$this->cache->fs->has( "{$type}-cs-{$mtime}.js" ) ) {
			$this->js->getFilterManager()->set('CoffeeScriptFilter', new CoffeeScriptFilter( get_option('am_coffeescript_path', '/usr/bin/coffee') ));
			$this->cache->fs->set( "{$type}-cs-{$mtime}.js", $this->js->createAsset( $this->coffee[$type], array($this->jsMin, 'CoffeeScriptFilter') )->dump() );
		}


		//Adds Coffeescript compiled stylesheet to normal js queue
		$script = $this->assetsPath . "{$type}-cs-{$mtime}.js";
		$this->scripts[$type][]= $script;
		$this->mTimes[$type][]= filemtime($script);
	}

	/**
	 * Returns header's inclusion for JS (if provided)
	 */
	public function headerServeScripts() {
		if ( empty($this->scripts['header']) )
			return false;

		$mtime = md5(implode('&', $this->mTimes['header']) . implode('&', $this->scripts['header']) );

		//Saves the asseticized header scripts
		if ( !$this->cache->fs->has( "head-{$mtime}.js" ) )
			$this->cache->fs->set( "head-{$mtime}.js", $this->js->createAsset( $this->scripts['header'], $this->js->getFilters() )->dump() );

		//Prints <script> inclusion in the page
		$this->dumpScriptData( 'header' );
		$this->dumpJs( "head-{$mtime}.js", false );
	}

	/**
	 * Returns footer's inclusion for JS (if provided)
	 */
	public function footerServe() {

		//Manages the scripts from CoffeeScript to be printed in the footer
		$this->generateCoffee('footer');

		if ( empty($this->scripts['footer']) )
			return false;

		$mtime = md5(implode('&', $this->mTimes['footer']) . implode('&', $this->scripts['footer']));

		//Saves the asseticized footer scripts
		if ( !$this->cache->fs->has( "foot-{$mtime}.js" ) )
			$this->cache->fs->set( "foot-{$mtime}.js", $this->js->createAsset( $this->scripts['footer'], $this->js->getFilters() )->dump() );

		//Prints <script> inclusion in the page
		$this->dumpScriptData( 'footer' );

		$async = false;
		if( get_option('am_async_flag', 1) )
			$async = true;
		$this->dumpJs( "foot-{$mtime}.js", $async );
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