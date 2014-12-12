<?php
namespace AssetsMinify\Assets\Css;

class Css {
	public function __construct($content, $cachefile, $manager) {
		$manager->cache->fs->set( $cachefile, $manager->createAsset( $content, array( 'CssRewrite' ) )->dump() );
	}
}