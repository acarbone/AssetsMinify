<?php
/**
 * Plugin Name: Assets Minify
 * Plugin URI: https://github.com/acarbone/AssetsMinify
 * Description: WordPress plugin to minify JS and CSS assets.
 * Author: Alessandro Carbone
 * Contributors: pputzer
 * Version: 2.0.2
 * Author URI: http://www.artera.it
 */
require dirname(__FILE__) . '/vendor/autoload.php';
AssetsMinify::getInstance();