<?php
use AssetsMinify\Log;

class LogTest extends WP_UnitTestCase {

	protected $plugin;

	public function setUp() {
		parent::setUp();
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

	public function testLogStorage() {
		$msg = 'This is the text error';
		$this->log->set('message', $msg);
		$this->assertEquals( $this->log->get('message'), $msg );
	}

	public function testLogDumpStorage() {
		$time = time();
		$key = 'message';
		$this->log->set( $key, array( $time, $time ) );
		$this->log->dumpStorage();

		$rows = $this->log->getAll();
		$last = $rows->offsetGet( $rows->count() - 2 );

		$this->assertEquals( $last['message'], "$key: 0s" );
	}
}