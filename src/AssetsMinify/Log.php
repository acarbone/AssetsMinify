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
			  $logger,
			  $storage;

	public static $filename = 'compile.log';

	/**
	 * Constructor
	 *
	 * @param object $cache The cache instance
	 * @return false if logging is disabled
	 */
	protected function __construct($params) {
		$this->active = get_option('am_log', 0) == 1;

		if ( empty($params) )
			return false;

		$cache = $params[0];

		if ( !$cache )
			return false;

		if ( !$this->isActive() )
			return false;

		$this->cache = $cache;

		$this->checkSize();

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
	 * The path of the log file 
	 *
	 * @param string
	 */
	public function checkSize() {
		$filepath = $this->getFilePath();

		if ( file_exists($filepath) && filesize($filepath) > 10000000 )
			$this->flush();
	}

	/**
	 * Flush the log
	 *
	 * @return boolean True if log has been flushed
	 */
	public function flush() {
		unlink($this->getFilePath());
		return true;
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

	/**
	 * Store a key, value pair
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return string The set value
	 */
	public function set($key, $value) {
		$this->storage[$key] = $value;
		return $value;
	}

	/**
	 * Retrieve a value saved within storage
	 *
	 * @param string $key
	 * @return mixed False if key isn't set else the value
	 */
	public function get($key) {
		return isset($this->storage[$key]) ? $this->storage[$key] : false;
	}

	/**
	 * Log the storage's content profiling the timing performances saved within it
	 *
	 * @return true
	 */
	public function dumpStorage() {
		foreach ( $this->storage as $key => $value ) {
			$line = "$key: ";
			if ( is_string($value) ) {
				$line .= $value;
			} else if ( is_array($value) && count($value) === 2 ) {
				$line .= ($value[1] - $value[0]) . "s";
			} else {
				$line .= json_encode($value);
			}
			$this->info($line);
		}

		$this->storage = array();
		return true;
	}
}