<?php
namespace AssetsMinify\Assets\Css;

/**
 * Css custom cache saving
 *
 * @author Alessandro Carbone <ale.carbo@gmail.com>
 */
class Css {
	/**
	 * Constructor
	 * 
	 * @param array $content The files to save to cache
	 * @param string $cachefile The cache file name
	 * @param object $manager The Factory object
	 */
	public function __construct($content, $cachefile, $manager) {
		$manager->cache->fs->set( $cachefile, $manager->createAsset( $content, array( 'CssRewrite' ) )->dump() );
	}
}