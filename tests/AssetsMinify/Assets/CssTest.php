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

	public function testEnqueue() {
		global $wp_styles;
		wp_enqueue_style( 'twentytwelve-style', get_stylesheet_uri() );

		//do_action( 'wp_print_styles' );
		$this->assertFalse( empty($wp_styles) );
		$this->assertTrue( is_array($wp_styles->queue) );
	}

	public function testExtract() {
		global $wp_styles;
		wp_enqueue_style( 'twentytwelve-style', get_stylesheet_uri() );

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
}