<?php
namespace AssetsMinify\Assets\Js;

use Assetic\Filter\CoffeeScriptFilter;

/**
 * Coffeescript custom cache saving
 *
 * @author Alessandro Carbone <ale.carbo@gmail.com>
 */
class Coffee {
	/**
	 * Constructor
	 * 
	 * @param array $content The files to save to cache
	 * @param string $cachefile The cache file name
	 * @param object $manager The Factory object
	 */
	public function __construct($content, $cachefile, $manager) {
		$manager->setFilter('CoffeeScriptFilter', new CoffeeScriptFilter( get_option('am_coffeescript_path', '/usr/bin/coffee') ));
		$manager->cache->fs->set( $cachefile, $manager->createAsset( $content, array('JSMin', 'CoffeeScriptFilter') )->dump() );
	}
}