<?php
class AssetsMinifyTest extends WP_UnitTestCase {
	public function testInit() {
		$plugin = AssetsMinify::bootstrap();
		$this->assertTrue( $plugin instanceof AssetsMinify );

		$singleton = AssetsMinify::bootstrap();
		$this->assertTrue( $singleton == $plugin );
	}
}