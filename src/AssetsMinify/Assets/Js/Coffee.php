<?php
namespace AssetsMinify\Assets\Js;

use Assetic\Filter\CoffeeScriptFilter;

class Coffee {
	public function __construct($content, $cachefile, $manager) {
		$manager->setFilter('CoffeeScriptFilter', new CoffeeScriptFilter( get_option('am_coffeescript_path', '/usr/bin/coffee') ));
		$manager->cache->fs->set( $cachefile, $manager->createAsset( $content, array('JSMin', 'CoffeeScriptFilter') )->dump() );
	}
}