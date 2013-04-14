<?php
require_once '../plugin.php';
require_once '../AssetsMinifyInit.php';

class AMInitTests extends WP_UnitTestCase {  

	protected $plugin;

	public function setUp() {
		parent::setUp();
		$this->plugin = new AssetsMinifyInit;
	}

	public function testInitialization() {
		$this->assertInstanceOf('AssetsMinifyInit', $this->plugin);
	}

	public function testAssetsFactory() {
		$this->assertInstanceOf('AssetsMinifyInit', $this->plugin);
		$this->assertFalse( null == $this->plugin->js );
		$this->assertFalse( null == $this->plugin->css );
	}

	public function testCacheDirectory() {
		$uploadsDir = wp_upload_dir();
		$this->assertTrue( is_dir($uploadsDir['basedir'] . '/am_assets/') );
	}

	public function testGC() {
		$uploadsDir = wp_upload_dir();
		$this->plugin->gc();
		$this->assertEquals( get_option('am_last_gc'), time() );
		foreach ( glob($uploadsDir['basedir'] . "/am_assets/*.*") as $filepath )
			$this->assertFalse( filemtime($filepath) <= time() - 864000 );
	}

}