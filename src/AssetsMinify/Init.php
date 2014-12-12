<?php
namespace AssetsMinify;

use AssetsMinify\Assets\Css;
use AssetsMinify\Assets\Factory;
use AssetsMinify\Assets\Js;


use Assetic\Filter\CoffeeScriptFilter;
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

	protected $scripts      = array( 'header' => array(), 'footer' => array() );
	protected $mTimes       = array( 'header' => array(), 'footer' => array() );

	protected $coffee       = array( 'header' => array(), 'footer' => array() );
	protected $mTimesCoffee = array( 'header' => array(), 'footer' => array() );

	/**
	 * Constructor of the class
	 */
	public function __construct() {
		$this->cache = new Cache;

		$this->js = new Js;
		$this->css = new Css;
		$this->css->setCache( $this->cache );

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
		$this->css->generate();


		//Manages the scripts from CoffeeScript to be printed in the header
		$this->generateCoffee('header');

		//Manages the scripts to be printed in the header
		$this->headerServeScripts();

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