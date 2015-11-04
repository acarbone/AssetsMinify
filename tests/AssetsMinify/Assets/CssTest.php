<?php
class CssTest extends WP_UnitTestCase {

	protected $plugin;

	public function setUp() {
		parent::setUp();
		$this->manager = new AssetsMinify\Init;
		$this->css = new AssetsMinify\Assets\Css($this->manager);
	}

	public function testFilters() {
		$filters = $this->css->getFilters();
		$this->assertTrue( is_array($filters) );
		$this->assertEquals( 'CssMin', $filters[0] );
		$this->assertEquals( 'CssRewrite', $filters[1] );
		$this->assertFalse( isset($filters[2]) );
	}

	public function testExtract() {
		global $wp_styles;
		wp_enqueue_style( 'style-extract', get_stylesheet_uri() );

		$external = 0;
		foreach( $wp_styles->queue as $handle ) {
			$style = str_replace( get_site_url(), "", $wp_styles->registered[$handle]->src );
			if ( strpos($style, "http") === 0 || strpos($style, "//") === 0 ) {
				$external++;
				continue;
			}
		}
		$this->css->extract();
		$this->assertEquals( count($wp_styles->queue), $external );
	}

	public function testGenerate() {
		wp_enqueue_style( 'style-generate', get_stylesheet_uri() );
		$this->css->extract();

		ob_start();
		$this->css->generate();
		$dump = ob_get_clean();
		$this->assertStringStartsWith( "<link href=", $dump );
	}

	public function testMediaInclusion() {
		wp_enqueue_style( 'style-media-include-screen', get_stylesheet_uri(), array(), false, 'screen' );
		wp_enqueue_style( 'style-media-include-print', get_stylesheet_uri(), array(), false, 'print' );
		$this->css->extract();

		ob_start();
		$this->css->generate();
		$dump = ob_get_clean();
		$this->assertContains( "media='screen'", $dump );
		$this->assertContains( "media='print'", $dump );
	}
}
