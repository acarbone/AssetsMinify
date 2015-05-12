<?php
namespace AssetsMinify\Assets\Js;

/**
 * Js custom cache saving
 *
 * @author Alessandro Carbone <ale.carbo@gmail.com>
 */
class Js {
	/**
	 * Constructor
	 * 
	 * @param array $content The files to save to cache
	 * @param string $cachefile The cache file name
	 * @param object $manager The Factory object
 	 * @param array $param Keyed array with settings for various Assetic classes.
	 */
	public function __construct($content, $cachefile, $manager, $params = null) {
		$manager->cache->fs->set( $cachefile, $manager->createAsset( $content, $manager->getFilters() )->dump() );
	}
}