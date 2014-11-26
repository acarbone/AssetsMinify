<?php
class AdminTest extends WP_UnitTestCase {

	protected $admin;

	public function setUp() {
		parent::setUp();
		$this->admin = new AssetsMinify\Admin;
	}

	public function testSettings() {
		ob_start();
		$this->admin->settings();
		$settingsTpl = ob_get_clean();
		$this->assertContains( "<input type='hidden' name='option_page' value='am_option_group' />", $settingsTpl );
	}

	public function testOptions() {
		$this->assertInternalType( "array", $this->admin->options() );
		$this->assertGreaterThan( 0, count($this->admin->options()) );
	}

	public function testEmptyCache() {
		$uploadsDir = wp_upload_dir();
		$cachedFilesBefore = count(glob($uploadsDir['basedir'] . '/am_assets/' . "*.*"));
		$this->admin->emptyCache();
		$cachedFilesAfter = count(glob($uploadsDir['basedir'] . '/am_assets/' . "*.*"));
		$this->assertGreaterThanOrEqual( $cachedFilesBefore, $cachedFilesAfter );
	}
}