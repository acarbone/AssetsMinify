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
		$wp_upload_dir;

	public $fs;

	/**
	 * Constructor
	 */
	public function __construct() {
		//WordPress directories detection
		$this->wp_upload_dir = wp_upload_dir();
		$this->url  = str_replace( 'http://', '//', $this->wp_upload_dir['baseurl'] ) . '/am_assets/';
		$this->path = $this->wp_upload_dir['basedir'];

		//Creates the uploads dir
		if ( !is_dir($this->path) ) {
			mkdir($this->path, 0777);
		}

		$this->path .=  '/am_assets/';

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
}