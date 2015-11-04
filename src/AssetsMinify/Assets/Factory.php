<?php
namespace AssetsMinify\Assets;

use Assetic\Factory\AssetFactory;
use Assetic\AssetManager;
use Assetic\FilterManager;

/**
 * Assets Factory.
 * Exposes the structure for assets (Js, Css) management.
 *
 * @author Alessandro Carbone <ale.carbo@gmail.com>
 */
abstract class Factory {

	protected $filters = array(),
			  $asset = null;

	public $cache = null;

	/**
	 * Constructor
	 *
	 * @param object $manager The init object used to retrive cache object
	 */
	public function __construct($manager) {
		$this->manager = $manager;
		if ( isset($this->manager->cache) )
			$this->cache = $this->manager->cache;

		$this->asset = new AssetFactory( ABSPATH );
		$this->asset->setAssetManager( new AssetManager );
		$this->asset->setFilterManager( new FilterManager );

		$this->setFilters();
	}

	/**
	 * Method used to set assets filters
	 */
	public function setFilters() {}

	/**
	 * Creates a new asset
	 *
	 * @param string $name Asset's name
	 * @param string $value Asset's value
	 * @return object
	 */
	public function createAsset( $name, $value ) {
		return $this->asset->createAsset( $name, $value, array(
				'root' => array( WP_CONTENT_DIR ),
				'output' => 'uploads/am_assets/*'
			)
		);
	}

	/**
	 * Sets a new filter
	 *
	 * @param string $name Filter's name
	 * @param string $value Filter's value
	 * @return object The instance of the factory
	 */
	public function setFilter( $name, $value ) {
		$this->asset->getFilterManager()->set( $name, $value );
		$this->filters []= $name;
		return $this;
	}

	/**
	 * Gets all the attached filters
	 *
	 * @return array
	 */
	public function getFilters() {
		return $this->filters;
	}

	/**
	 * Guess absolute path from file URL
	 *
	 * @param string $file_url File's url
	 * @return string
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
		$regex  = '@^' . str_replace( 'http\://','(https?\:)?\/\/', preg_quote( $url )) . '@';
		return $regex;
	}
}