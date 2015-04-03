<?php
namespace AssetsMinify;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Log Manager.
 * It's the manager for every log operation.
 *
 * @author Alessandro Carbone <ale.carbo@gmail.com>
 */
class Log {

	protected $active;

	public static $filename = 'compile.log';

	/**
	 * Constructor
	 *
	 * @param object $cache The cache instance
	 * @return false if logging is disabled
	 */
	public function __construct($cache) {
		$this->active = get_option('am_log', 0) == true;

		if ( !$cache )
			return false;

		if ( !$this->isActive() )
			return false;

		$this->cache = $cache;

		// create a log channel
		$this->logger = new Logger('Compile');
		$this->logger->pushHandler( new StreamHandler( $this->cache->getPath() . self::$filename, Logger::WARNING ) );

		$this->logger->addWarning('asd');
	}

	/**
	 * True if logging is enabled.
	 *
	 * @return true if logging is enabled
	 */
	public function isActive() {
		return $this->active;
	}
}