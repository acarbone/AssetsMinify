<?php
/*
Plugin Name: Assets Minify
Plugin URI: https://github.com/acarbone/AssetsMinify
Description: WordPress plugin to minify JS and CSS assets.
Author: Alessandro Carbone
Version: 0.1.0beta
Author URI: http://www.artera.it
*/

//Autoloader
spl_autoload_register(function( $classname ) {
	$filename = str_replace("\\", "/", __DIR__ . "/lib/$classname.php");

	if ( file_exists( $filename ) )
		include_once $filename;
});

//Start
if ( !is_admin() )
	require_once 'AssetsMinifyInit.php';
else
	require_once 'AssetsMinifyAdmin.php';