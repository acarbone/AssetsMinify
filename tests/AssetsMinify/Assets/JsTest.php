<?php
class JsTest extends WP_UnitTestCase {

	protected $plugin;

	public function setUp() {
		parent::setUp();
		$this->manager = new AssetsMinify\Init;
		$this->js = new AssetsMinify\Assets\Js($this->manager);
	}

	public function testFilters() {
		$filters = $this->js->getFilters();
		$this->assertTrue( is_array($filters) );
		$this->assertEquals( 'JSqueeze', $filters[0] );
		$this->assertFalse( isset($filters[1]) );
	}

	public function testExtract() {
		global $wp_scripts;
		wp_enqueue_script( 'script-extract', get_template_directory_uri() . '/js/functions.js', array(), '1.0', true );

		$external = 0;
		foreach( $wp_scripts->queue as $handle ) {
			$script = str_replace( get_site_url(), "", $wp_scripts->registered[$handle]->src );
			if ( strpos($script, "http") === 0 || strpos($script, "//") === 0 ) {
				$external++;
				continue;
			}
		}
		$this->js->extract();
		$this->assertEquals( count($wp_scripts->queue), $external );
	}

	public function testGenerate() {
		wp_enqueue_script( 'script-generate', get_template_directory_uri() . '/js/functions.js', array(), '1.0', true );
		$this->js->extract();

		ob_start();
		$this->js->generate( 'footer' );
		$dump = ob_get_clean();
		$this->assertStringStartsWith( "<script type='text/javascript'", $dump );
	}

	public function testLocalizeScripts() {
		global $wp_scripts;
		wp_enqueue_script( 'localize', get_template_directory_uri() . '/js/functions.js', array(), '1.0', true );
		wp_localize_script( 'localize', 'localizeVar', array(
			'var' => 'value'
		));
		$this->js->extract();

		ob_start();
		$this->js->generate( 'footer' );
		$footer = ob_get_clean();

		$rawJs = 'var localizeVar = {"var":"value"};';
		$minfiedJs = "var localizeVar={'var':'value'};";

		$this->assertSame( $rawJs, $wp_scripts->registered['localize']->extra['data'] );
		$this->assertContains( $minfiedJs, $footer );
	}
}
