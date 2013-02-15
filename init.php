<?php
/**
 * @package Assets Minify
 */
spl_autoload_register(function( $classname ) {
	$filename = str_replace("\\", "/", __DIR__ . "/$classname.php");

	if ( file_exists( $filename ) )
		include_once $filename;
});

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
/**
 * Class that holds plugin's logic.
 */
class AS_MINIFY_Init {

	public function __construct() {

		$js = new AssetCollection(array(
		    new GlobAsset('/home/alessandro/development/wordpress/wp-content/themes/twentytwelve/js/*'),
		));
		// the code is merged when the asset is dumped
		//echo $js->dump();

	}
}

// Globalize the var first as it's needed globally.
global $AS_MINIFY;
$AS_MINIFY = new AS_MINIFY_Init();