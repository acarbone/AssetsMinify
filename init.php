<?php
/**
 * @package Assets Minify
 */
use Assetic;
set_include_path ( get_include_path() . PATH_SEPARATOR . __DIR__  ); 

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
/**
 * Class that holds plugin's logic.
 */
class AS_MINIFY_Init {

	public function __construct() {

		try{
			$js = new AssetCollection(array(
			    new GlobAsset('/home/alessandro/development/wordpress/wp-content/themes/twentytwelve/js/*'),
			));
		} catch( Exception $e ) {

		}

		// the code is merged when the asset is dumped
		//echo $js->dump();

	}
}

// Globalize the var first as it's needed globally.
global $AS_MINIFY;
$AS_MINIFY = new AS_MINIFY_Init();