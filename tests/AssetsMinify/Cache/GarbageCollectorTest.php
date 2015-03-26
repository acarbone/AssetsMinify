<?php
class GarbageCollectorTest extends WP_UnitTestCase {

	protected $plugin;

	public function setUp() {
		parent::setUp();
		$this->cache = new AssetsMinify\Cache;
		$this->gc = new AssetsMinify\Cache\GarbageCollector( $this->cache );
	}

	protected function getCachedFiles() {
		$files = glob( $this->cache->getPath() . "*.*" );
		if ( $files === false )
			return 0;
		return count($files);
	}

	public function testCodeOption() {
		$code = get_option( AssetsMinify\Cache\GarbageCollector::$code, 0 );
		$this->assertGreaterThan( 0, $code );
	}

	public function testRefresh() {
		$before = $this->getCachedFiles();
		$this->gc->refresh();
		$after = $this->getCachedFiles();
		$this->assertGreaterThanOrEqual( $before, $after );
	}
}