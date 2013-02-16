<?php
/**
 * @package Assets Minify
 */

namespace AssetsMinify;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\AssetCache;
use Assetic\AssetManager;
use Assetic\Asset\GlobAsset;
use Assetic\AssetWriter;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\Worker\CacheBustingWorker;
use Assetic\Cache\FilesystemCache;

/**
 * Class that holds plugin's logic.
 */
class Init {

	protected $styles = array(),
		$scripts = array(
			'header' => array(),
			'footer' => array(),
		);

	public function __construct() {

		/*$js = new AssetCollection(array(
		    new GlobAsset('/home/alessandro/development/sofinter/wp-content/themes/sofinter-restyle/js/*'),
		));*/
		// the code is merged when the asset is dumped
		//echo $js->dump();

		add_action( 'wp_print_scripts', array( $this, 'extractScripts' ) );
		add_action( 'wp_head', array( $this, 'headerScripts' ) );

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
			$this->scripts[ $where ][ $handle ] = new FileAsset(
				getcwd() . $wp_scripts->registered[$handle]->src
			);

			//Remove scripts from the queue so this plugin will be
			//responsible to include all the scripts.
			$wp_scripts->dequeue( $handle );

		}
		return true;
	}

	public function headerScripts() {
		//$factory = new AssetFactory( getcwd() . "/wp-content/uploads/" );
		$js = new AssetCache(
		    new FileAsset('/home/alessandro/development/wordpress/wp-includes/js/admin-bar.min.js'),
    		new FilesystemCache( getcwd() . "/wp-content/uploads/" )
		);
		$js->dump();
		return true;
	}

	public function enqueueStyles() {

		return true;
	}
}

new Init();