<?php
namespace AssetsMinify\Assets\Css;

use Assetic\Filter\LessphpFilter;

class Less {
	public function __construct($content, $cachefile, $manager) {
		$manager->setFilter('Lessphp', new LessphpFilter);
		$manager->cache->fs->set( $cachefile, $manager->createAsset( $content, array( 'Lessphp', 'CssRewrite' ) )->dump() );
	}
}