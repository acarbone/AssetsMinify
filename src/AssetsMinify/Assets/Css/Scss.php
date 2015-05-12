<?php
namespace AssetsMinify\Assets\Css;

use Assetic\Filter\CompassFilter;
use Assetic\Filter\ScssphpFilter;
use Assetic\Filter\CssRewriteFilter;

/**
 * Scss custom cache saving
 *
 * @author Alessandro Carbone <ale.carbo@gmail.com>
 */
class Scss extends \AssetsMinify\Assets\Factory {
	/**
	 * @var array
	 */
	private $params = null;

	/**
	 * Constructor
	 * 
	 * @param array $content The files to save to cache
	 * @param string $cachefile The cache file name
	 * @param object $manager The Factory object
	 */
	public function __construct($content, $cachefile, $manager, $params = null) {
		$this->manager = $manager;
		$this->params = $params;
		parent::__construct( $this );
		$manager->cache->fs->set( $cachefile, $this->createAsset( $content, $this->getFilters() )->dump() );
	}

	public function setFilters() {
		if ( get_option('am_use_compass', 0) != 0 ) {
			//Defines compass filter instance and sprite images paths
			$compassInstance = new CompassFilter( get_option('am_compass_path', '/usr/bin/compass') );
			$compassInstance->setImagesDir(get_theme_root() . "/" . get_template() . "/images");
			$compassInstance->setGeneratedImagesPath( $this->manager->cache->getPath() );
			$compassInstance->setHttpGeneratedImagesPath( site_url() . str_replace( getcwd(), '', $this->manager->cache->getPath() ) );
			$this->setFilter('Compass', $compassInstance);
			$filter = 'Compass';
		} else {
			$scssphpFilter = new ScssphpFilter();

			if ( is_array($this->params) ) {
				if ( ! empty($this->params['addImportPath']) ) {
					if ( is_array($this->params['addImportPath'] ) ) {
						foreach ( $this->params['addImportPath'] as $value ) {
							$scssphpFilter->addImportPath($value);
						}
					} else {
						$scssphpFilter->addImportPath($this->params['addImportPath']);
					}
				}
			}

			$this->setFilter('Scssphp', $scssphpFilter);
			$filter = 'Scssphp';
		}

		$this->setFilter('CssRewrite', new CssRewriteFilter);
	}
}