<?php
/**
 * @package Assets Minify
 */

namespace AssetsMinify;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;

/**
 * Class that holds plugin's logic.
 */
class Init {

	protected $js, $css;

	public function __construct() {

		/*$js = new AssetCollection(array(
		    new GlobAsset('/home/alessandro/development/sofinter/wp-content/themes/sofinter-restyle/js/*'),
		));*/
		// the code is merged when the asset is dumped
		//echo $js->dump();

		$this->js  = $this->enqueueScripts();
		$this->css = $this->enqueueStyles();
	}

	public function enqueueScripts() {

		return true;
	}

	public function enqueueStyles() {

		return true;
	}
}

// Globalize the var first as it's needed globally.
new Init();