<?php
use AssetsMinify\Log;

class LogTest extends WP_UnitTestCase {

	protected $plugin;

	public function setUp() {
		parent::setUp();
		update_option('am_log', 1);
		$this->cache = new AssetsMinify\Cache;
		$this->log = Log::getInstance($this->cache);
	}

	public function testInitialization() {
		$this->assertInstanceOf('AssetsMinify\Log', $this->log);
	}

	public function testActivation() {
		$this->assertTrue( $this->log->isActive() );
	}

	// No new instance
	public function testSingletonProtection() {
		update_option('am_log', 0);
		$cache = new AssetsMinify\Cache;
		$log = Log::getInstance($cache);
		$this->assertTrue( $log->isActive() );
	}

	public function testLogFileExists() {
		$this->log->info('First message');
		$this->assertTrue( file_exists($this->log->getFilePath()) );
	}

	public function testLogInfo() {
		$msg = 'This is the text error';
		$this->log->info($msg);
		$rows = $this->log->getAll();

		$last = $rows->offsetGet( $rows->count() - 2 );
		$this->assertEquals( $last['message'], $msg );
	}
}