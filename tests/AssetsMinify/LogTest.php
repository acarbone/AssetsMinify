<?php
use AssetsMinify\Log;

class LogTest extends WP_UnitTestCase {

	protected $plugin;

	public function setUp() {
		parent::setUp();
		$this->cache = new AssetsMinify\Cache;
		$this->log = new Log($this->cache);
	}

	public function testInitialization() {
		$this->assertInstanceOf('AssetsMinify\Log', $this->log);
	}

	public function testActivation() {
		update_option('am_log', 1);
		$log = new Log($this->cache);
		$this->assertTrue( $log->isActive() );
	}

	public function testDeactivation() {
		update_option('am_log', 0);
		$log = new Log($this->cache);
		$this->assertFalse( $log->isActive() );
	}

	public function testLogFileExists() {
		$this->assertTrue( file_exists($this->cache->getPath() . Log::$filename) );
	}
}