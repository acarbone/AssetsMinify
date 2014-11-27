<?php
class InitTest extends WP_UnitTestCase {

	protected $plugin;

	public function setUp() {
		parent::setUp();
		$this->plugin = new AssetsMinify\Init;
	}

	public function testInitialization() {
		$this->assertInstanceOf('AssetsMinify\Init', $this->plugin);
	}

	public function testGC() {
		$this->assertFalse(null == $this->plugin->gc);
		$this->assertInstanceOf('AssetsMinify\Cache\GarbageCollector', $this->plugin->gc);
	}
}