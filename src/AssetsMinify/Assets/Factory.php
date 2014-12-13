<?php
namespace AssetsMinify\Assets;

use Assetic\Factory\AssetFactory;
use Assetic\AssetManager;
use Assetic\FilterManager;

class Factory {

	protected $filters = array(),
			  $asset = null;

	public function __construct($manager) {
		$this->manager = $manager;
		$this->cache = $this->manager->cache;

		$this->asset = new AssetFactory( ABSPATH );
		$this->asset->setAssetManager( new AssetManager );
		$this->asset->setFilterManager( new FilterManager );

		$this->setFilters();
	}

	public function setFilters() {}

	public function createAsset( $name, $value ) {
		return $this->asset->createAsset( $name, $value);
	}

	public function setFilter( $name, $value ) {
		$this->asset->getFilterManager()->set( $name, $value );
		$this->filters []= $name;
		return $this;
	}

	public function getFilters() {
		return $this->filters;
	}

	/**
	 * Guess absolute path from file URL
	 */
	public function guessPath( $file_url ) {

		$components = parse_url($file_url);

		// Check we have at least a path
		if( !isset($components['path']) )
			return false;

		$file_path = false;
		$wp_plugin_url = plugins_url();
		$wp_content_url = content_url();

		// Script is enqueued from a plugin

		$url_regex = $this->getUrlRegex($wp_plugin_url);
		if( preg_match($url_regex, $file_url) > 0 )
			$file_path = WP_PLUGIN_DIR . preg_replace($url_regex, '', $file_url);

		// Script is enqueued from a theme
		$url_regex = $this->getUrlRegex($wp_content_url);
		if( preg_match($url_regex, $file_url) > 0 )
			$file_path = WP_CONTENT_DIR . preg_replace($url_regex, '', $file_url);

		// Script is enqueued from wordpress
		if( strpos($file_url,  WPINC) !== false )
			$file_path = untrailingslashit(ABSPATH) . $file_url;

		return $file_path;
	}
	
	/**
	 * Returns Regular Expression string to match an URL.
	 *
	 * @param string $url The URL to be matched.
	 * @return string The regular expression matching the URL.
	 */
	protected function getUrlRegex( $url ) {
		$regex  = '@^' . str_replace( 'http\://','https?\:\/\/', preg_quote( $url )) . '@';
		return $regex;
	}
}