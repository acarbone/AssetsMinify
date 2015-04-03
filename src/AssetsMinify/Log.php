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

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->active = get_option('am_log', 0) == true;
	}

	public function isActive() {
		return $this->active;
	}
}