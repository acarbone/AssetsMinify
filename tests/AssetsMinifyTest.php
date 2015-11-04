<?php
class AssetsMinifyTest extends WP_UnitTestCase {
	public function testInit() {
		$plugin = AssetsMinify::getInstance();
		$this->assertTrue( $plugin instanceof AssetsMinify );

		$singleton = AssetsMinify::getInstance();
		$this->assertTrue( $singleton == $plugin );
	}
}