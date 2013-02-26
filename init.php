<?php
/**
 * @package Assets Minify
 */

namespace AssetsMinify;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\FilterManager;
use Assetic\AssetManager;
use Assetic\Factory\AssetFactory;
use Assetic\Filter\JSMinFilter;

/**
 * Class that holds plugin's logic.
 */
class Init {

	public $factory;
	protected $assetsPath;
	protected $styles = array(), $filters = array(),
		$scripts = array(
			'header' => array(),
			'footer' => array(),
		);

	public function __construct() {

		$this->factory = new AssetFactory( getcwd() );
		$this->factory->setAssetManager(new AssetManager);
		$this->factory->setFilterManager(new FilterManager);
		$this->factory->getFilterManager()->set('JSMin', new JSMinFilter);
		$this->filters []= 'JSMin';
		$this->assetsPath = AS_MINIFY_PATH . 'assets/';

		if ( !is_dir($this->assetsPath) )
			mkdir($this->assetsPath, 0777);

		add_action( 'wp_print_scripts', array( $this, 'extractScripts' ) );
		add_action( 'wp_head', array( $this, 'headerScripts' ) );
		add_action( 'wp_footer', array( $this, 'footerScripts' ) );

		$this->css = $this->enqueueStyles();
	}

	public function extractScripts() {
		global $wp_scripts;

		foreach( $wp_scripts->queue as $handle ) {

			$where = 'footer';
			//Unfortunately not every WP plugin developer is a JS ninja.
			//So... let's put it in the header.
			if ( empty($wp_scripts->registered[$handle]->extra) )
				$where = 'header';

			//Save the source filename for every script enqueued.
			$this->scripts[ $where ][ $handle ] = getcwd() . str_replace( "http://{$_SERVER['SERVER_NAME']}", "", $wp_scripts->registered[$handle]->src );

			//Remove scripts from the queue so this plugin will be
			//responsible to include all the scripts.
			$wp_scripts->dequeue( $handle );

		}
		return true;
	}

	public function headerScripts() {
		file_put_contents( $this->assetsPath . "head.js", $this->factory->createAsset( $this->scripts['header'], $this->filters )->dump() );
		echo "<script type='text/javascript' src='" . AS_MINIFY_URL . "assets/head.js'></script>";
		return true;
	}

	public function footerScripts() {
		file_put_contents( $this->assetsPath . "foot.js", $this->factory->createAsset( $this->scripts['footer'], $this->filters )->dump() );
		echo "<script type='text/javascript' src='" . AS_MINIFY_URL . "assets/foot.js'></script>";
		return true;
	}

	public function enqueueStyles() {

		return true;
	}
}

new Init();