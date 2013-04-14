<?php
require_once '../plugin.php';
require_once '../AssetsMinifyAdmin.php';

class AMAdminTests extends WP_UnitTestCase {  

	protected $plugin;

	public function setUp() {
		parent::setUp();
	}

	public function testAMInitialization() {
		$this->assertTrue( function_exists('amAutoloader') );
		$this->assertTrue( function_exists('amPluginsLoaded') );
		$this->plugin = amPluginsLoaded();
	}
}