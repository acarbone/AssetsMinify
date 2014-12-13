<?php
namespace AssetsMinify\Cache;

/**
 * Garbage Collector.
 * Manages the removing of outdated cached files of AM.
 *
 * @author Alessandro Carbone <ale.carbo@gmail.com>
 */
class GarbageCollector {

	protected $offset = 86400;

	public static $code = 'am_last_gc';
	public static $period = 10;

	/**
	 * Constructor. Checks if the last check provided is < $period days ago
	 *
	 * @param object $cache The cache manager used to get the cache path
	 */
	public function __construct($cache) {
		$this->cache = $cache;
		$this->offset *= self::$period;

		//Every {$period} days the cache is refreshed
		if ( get_option( self::$code, 0 ) <= time() - $this->offset ) {
			$this->refresh();
		}

		update_option( self::$code, time() );
	}

	/**
	 * Refreshes the outdated cached files
	 */
	public function refresh() {
		$files = glob( $this->cache->getPath() . "*.*" );
		if ( $files === false )
			return false;

		foreach ( $files  as $filepath ) {
			//If the file is older than $period days then is removed
			if ( filemtime($filepath) <= time() - $this->offset ) {
				unlink($filepath);
			}
		}
	}
}