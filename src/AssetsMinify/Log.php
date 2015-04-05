<?php
namespace AssetsMinify;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use Dubture\Monolog\Reader\LogReader;

/**
 * Log Manager.
 * It's the manager for every log operation.
 *
 * @author Alessandro Carbone <ale.carbo@gmail.com>
 */
class Log extends Pattern\Singleton {

	protected $active,
			  $cache,
			  $logger;

	public static $filename = 'compile.log';

	/**
	 * Constructor
	 *
	 * @param object $cache The cache instance
	 * @return false if logging is disabled
	 */
	protected function __construct($params) {
		$this->active = get_option('am_log', 0) == true;

		if ( empty($params) )
			return false;

		$cache = $params[0];

		if ( !$cache )
			return false;

		if ( !$this->isActive() )
			return false;

		$this->cache = $cache;

		// create a log channel
		$this->logger = new Logger('Compile');
		$this->logger->pushHandler( new StreamHandler( $this->getFilePath(), Logger::DEBUG ) );
	}

	/**
	 * The path of the log file 
	 *
	 * @param string
	 */
	public function getFilePath() {
		return $this->cache->getPath() . self::$filename;
	}

	/**
	 * Log an info level message.
	 *
	 * @param string $message The message to log
	 * @return boolean True if log has been written, only if logging is enabled
	 */
	public function info($message) {
		if ( !$this->isActive() )
			return false;

		$this->logger->info($message);
		return true;
	}

	/**
	 * Get all saved logs
	 *
	 * @return array
	 */
	public function getAll() {
		return new LogReader( $this->getFilePath() );
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