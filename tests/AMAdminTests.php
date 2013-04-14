<?php
require_once '../plugin.php';
require_once '../AssetsMinifyAdmin.php';

class AMAdminTests extends WP_UnitTestCase {  

	protected $plugin;

	public function setUp() {
		parent::setUp();
		$this->plugin = amPluginsLoaded();
	}

	public function testInitialization() {
		$this->assertTrue( function_exists('amAutoloader') );
		$this->assertTrue( function_exists('amPluginsLoaded') );
		$this->assertInstanceOf('AssetsMinifyAdmin', $this->plugin);
	}

}