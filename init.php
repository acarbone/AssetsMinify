<?php
/**
 * @package Assets Minify
 */

/**
 * Class that holds plugin's logic.
 */
class AS_MINIFY_Init {

	public function __construct() {
	}
}

// Globalize the var first as it's needed globally.
global $AS_MINIFY;
$AS_MINIFY = new AS_MINIFY_Init();