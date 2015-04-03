<?php
use AssetsMinify\Log;

class LogTest extends WP_UnitTestCase {

	protected $plugin;

	public function setUp() {
		parent::setUp();
		$this->log = new Log;
	}

	public function testInitialization() {
		$this->assertInstanceOf('AssetsMinify\Log', $this->log);
	}

	public function testActivation() {
		update_option('am_log', 1);
		$log = new Log;
		$this->assertTrue( $log->isActive() );
	}

	public function testDeactivation() {
		update_option('am_log', 0);
		$log = new Log;
		$this->assertFalse( $log->isActive() );
	}
}