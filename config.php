<?php
/*
Plugin Name: Assets Minify
Plugin URI: https://github.com/acarbone/AssetsMinify
Description: WordPress plugin to minify JS and CSS assets.
Author: Alessandro Carbone
Version: 0.1.0
Author URI: http://www.artera.it
*/

//Define
if ( !defined('AS_MINIFY_URL') )
	define( 'AS_MINIFY_URL', plugin_dir_url( __FILE__ ) );
if ( !defined('AS_MINIFY_PATH') )
	define( 'AS_MINIFY_PATH', plugin_dir_path( __FILE__ ) );
if ( !defined('AS_MINIFY_BASENAME') )
	define( 'AS_MINIFY_BASENAME', plugin_basename( __FILE__ ) );

define( 'AS_MINIFY_FILE', __FILE__ );

//Autoloader
spl_autoload_register(function( $classname ) {
	$filename = str_replace("\\", "/", __DIR__ . "/$classname.php");

	if ( file_exists( $filename ) )
		include_once $filename;
});

//Start
if ( !is_admin() ) {
	require AS_MINIFY_PATH . 'init.php';
}