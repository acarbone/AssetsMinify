<?php
namespace AssetsMinify\Assets\Css;

use Assetic\Filter\LessphpFilter;

/**
 * Less custom cache saving
 *
 * @author Alessandro Carbone <ale.carbo@gmail.com>
 */
class Less {
	/**
	 * Constructor
	 * 
	 * @param array $content The files to save to cache
	 * @param string $cachefile The cache file name
	 * @param object $manager The Factory object
	 */
	public function __construct($content, $cachefile, $manager) {
		$manager->setFilter('Lessphp', new LessphpFilter);
		$manager->cache->fs->set( $cachefile, $manager->createAsset( $content, array( 'Lessphp', 'CssRewrite' ) )->dump() );
	}
}