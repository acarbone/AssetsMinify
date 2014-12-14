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

	public function testCache() {
		$this->assertFalse(null == $this->plugin->cache);
		$this->assertInstanceOf('AssetsMinify\Cache', $this->plugin->cache);
	}

	public function testAssetsManagers() {
		$this->assertFalse( null == $this->plugin->js );
		$this->assertFalse( null == $this->plugin->css );
	}
}