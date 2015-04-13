<?php
namespace AssetsMinify;

use Assetic\Cache\FilesystemCache;

/**
 * Cache Manager.
 * It's the manager for every cache read/save operation about AssetsMinify assets.
 *
 * @author Alessandro Carbone <ale.carbo@gmail.com>
 */
class Cache {

	protected $path,
		$url,
		$wp_upload_dir,
		$updated = false;

	public static $directory = 'am_assets';
	public $fs,
		   $gc;

	/**
	 * Constructor
	 */
	public function __construct() {
		//WordPress directories detection
		$this->wp_upload_dir = wp_upload_dir();
		$this->url  = str_replace( 'http://', '//', $this->wp_upload_dir['baseurl'] ) . '/' . self::$directory . '/';
		$this->path = $this->wp_upload_dir['basedir'];

		//Creates the uploads dir
		if ( !is_dir($this->path) ) {
			mkdir($this->path, 0777);
		}

		$this->path .=  '/' . self::$directory . '/';

		//Creates the AM cache dir
		if ( !is_dir($this->path) ) {
			mkdir($this->path, 0777);
		} else {
			//Calls the Garbage Collector that outdated cached files.
			$this->gc = new Cache\GarbageCollector( $this );
		}

		//Manager for Filesystem management
		$this->fs = new FilesystemCache( $this->path );
	}

	/**
	 * Gets the AssetsMinify cache path
	 *
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * Gets the AssetsMinify cache url
	 *
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * Sets the updated status for the cache
	 *
	 * @return true
	 */
	public function update() {
		return $this->updated = true;
	}

	/**
	 * Gets the cache status
	 *
	 * @return boolean True if cache has been updated
	 */
	public function isUpdated() {
		return $this->updated;
	}

	/**
	 * Flush the whole cache
	 *
	 * @return true
	 */
	public function flush() {
		$uploadsDir = wp_upload_dir();
		$filesList = glob($this->path . "*.*");
		if ( $filesList !== false ) {
			array_map('unlink', $filesList);
		}
		return true;
	}
}