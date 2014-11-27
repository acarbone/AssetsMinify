<?php
namespace AssetsMinify;

use Assetic\Cache\FilesystemCache;

class Cache extends Pattern\Container {

	protected $path,
		$url,
		$wp_upload_dir;

	public $fs;

	public function __construct() {
		$this->wp_upload_dir = wp_upload_dir();
		$this->url  = str_replace( 'http://', '//', $this->wp_upload_dir['baseurl'] ) . '/am_assets/';
		$this->path = $this->wp_upload_dir['basedir'] . '/am_assets/';

		//Creates the AM cache dir
		if ( !is_dir($this->path) ) {
			mkdir($this->path, 0777);
		} else {
			$this->gc = new Cache\GarbageCollector( $this );
			$this->gc->refresh();
		}


		//Manager for Filesystem management
		$this->fs = new FilesystemCache( $this->path );
	}

	public function getPath() {
		return $this->path;
	}
}