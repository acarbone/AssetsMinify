<?php
namespace AssetsMinify\Assets\Css;

use Assetic\Filter\LessphpFilter;
use Assetic\Filter\CssRewriteFilter;

/**
 * Less custom cache saving
 *
 * @author Alessandro Carbone <ale.carbo@gmail.com>
 */
class Less extends \AssetsMinify\Assets\Factory {
	/**
	 * Constructor
	 * 
	 * @param array $content The files to save to cache
	 * @param string $cachefile The cache file name
	 * @param object $manager The Factory object
 	 * @param array $param Keyed array with settings for various Assetic classes.
	 */
	public function __construct($content, $cachefile, $manager, $params = null) {
		parent::__construct( $this );
		$manager->cache->fs->set( $cachefile, $this->createAsset( $content, $this->getFilters() )->dump() );
	}

	public function setFilters() {
		$this->setFilter('Lessphp', new LessphpFilter)
			->setFilter('CssRewrite', new CssRewriteFilter);
	}
}