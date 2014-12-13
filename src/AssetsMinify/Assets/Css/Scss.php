<?php
namespace AssetsMinify\Assets\Css;

use Assetic\Filter\CompassFilter;
use Assetic\Filter\ScssphpFilter;

/**
 * Scss custom cache saving
 *
 * @author Alessandro Carbone <ale.carbo@gmail.com>
 */
class Scss {
	/**
	 * Constructor
	 * 
	 * @param array $content The files to save to cache
	 * @param string $cachefile The cache file name
	 * @param object $manager The Factory object
	 */
	public function __construct($content, $cachefile, $manager) {
		if ( get_option('am_use_compass', 0) != 0 ) {
			//Defines compass filter instance and sprite images paths
			$compassInstance = new CompassFilter( get_option('am_compass_path', '/usr/bin/compass') );
			$compassInstance->setImagesDir(get_theme_root() . "/" . get_template() . "/images");
			$compassInstance->setGeneratedImagesPath( $manager->cache->getPath() );
			$compassInstance->setHttpGeneratedImagesPath( site_url() . str_replace( getcwd(), '', $manager->cache->getPath() ) );
			$manager->setFilter('Compass', $compassInstance);
			$filter = 'Compass';
		} else {
			$manager->setFilter('Scssphp', new ScssphpFilter);
			$filter = 'Scssphp';
		}

		$manager->cache->fs->set( $cachefile, $manager->createAsset( $content, array( $filter, 'CssRewrite' ) )->dump() );
	}
}