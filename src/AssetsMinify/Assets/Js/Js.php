<?php
namespace AssetsMinify\Assets\Js;


class Js {
	public function __construct($content, $cachefile, $manager) {
		$manager->cache->fs->set( $cachefile, $manager->createAsset( $content, $manager->getFilters() )->dump() );
	}
}