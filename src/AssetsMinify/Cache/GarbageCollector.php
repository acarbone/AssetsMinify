<?php
namespace AssetsMinify\Cache;

/**
 * Garbage collector for 10 days old files
 */
class GarbageCollector {

	protected $offset = 86400;

	public static $code = 'am_last_gc';
	public static $period = 10;

	public function __construct($cache) {
		$this->cache = $cache;
		$this->offset *= self::$period;

		//Every {$period} days the cache is refreshed
		if ( get_option( self::$code, 0 ) <= time() - $this->offset ) {
			$this->refresh();
		}

		update_option( self::$code, time() );
	}

	public function refresh() {
		$files = glob( $this->cache->getPath() . "*.*" );
		if ( $files === false )
			return false;

		foreach ( $files  as $filepath ) {
			//If the file is older than 10 days then is removed
			if ( filemtime($filepath) <= time() - $this->offset ) {
				unlink($filepath);
			}
		}
	}
}