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
		$files = glob($uploadsDir['basedir'] . "/am_assets/*.*");
		if ( $files === false )
			return false;
		foreach ( $files as $filepath )
			$this->assertFalse( filemtime($filepath) <= time() - 864000 );
	}

	public function testExtractStyles() {
		global $wp_styles;
		wp_enqueue_style( 'twentytwelve-style', get_stylesheet_uri() );

		//do_action( 'wp_print_styles' );
		$this->assertFalse( empty($wp_styles) );
		$this->assertTrue( is_array($wp_styles->queue) );

	}

	public function testStylesQueueManagement() {
		global $wp_styles;
		wp_enqueue_style( 'twentytwelve-style', get_stylesheet_uri() );

		$external = 0;
		$_SERVER['SERVER_NAME'] = 'wordpress.local';
		foreach( $wp_styles->queue as $handle ) {
			$style = str_replace( "http://{$_SERVER['SERVER_NAME']}", "", $wp_styles->registered[$handle]->src );
			if ( strpos($style, "http") === 0 || strpos($style, "//") === 0 ) {
				$external++;
				continue;
			}
			$wp_styles->dequeue( $handle );
		}
		$this->assertEquals( count($wp_styles->queue), $external );
	}

	public function testExtractScripts() {
		global $wp_scripts;
		wp_enqueue_script( 'twentytwelve-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '1.0', true );

		//do_action( 'wp_print_styles' );
		$this->assertFalse( empty($wp_scripts) );
		$this->assertTrue( is_array($wp_scripts->queue) );

	}

	public function testScriptsQueueManagement() {
		global $wp_scripts;
		wp_enqueue_script( 'twentytwelve-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '1.0', true );
		wp_enqueue_script( 'script4', '//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js', array(), '1.0', true );

		$external = 0;
		$_SERVER['SERVER_NAME'] = 'wordpress.local';
		foreach( $wp_scripts->queue as $handle ) {
			$style = str_replace( "http://{$_SERVER['SERVER_NAME']}", "", $wp_scripts->registered[$handle]->src );
			if ( strpos($style, "http") === 0 || strpos($style, "//") === 0 ) {
				$external++;
				continue;
			}
			$wp_scripts->dequeue( $handle );
		}
		$this->assertEquals( count($wp_scripts->queue), $external );
		$this->assertEquals( count($wp_scripts->queue), 1 );
	}

	public function testLocalizeScripts() {
		global $wp_scripts;

		wp_enqueue_script( 'twentytwelve-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '1.0', true );

		wp_localize_script( 'twentytwelve-navigation', 'twentytwelveNavigation', array(
			'var' => 'value'
		));

		// Temporarily change the cwd to ABSPATH so that extractScripts() can
		// find the scripts.
		$cwd = getcwd();
		chdir( ABSPATH );

		$this->plugin->extractScripts();

		chdir( $cwd );

		ob_start();
		$this->plugin->footerServe();
		$footer = ob_get_clean();

		ob_start();
		$this->plugin->headerServe();
		$header = ob_get_clean();

		$rawJs = 'var twentytwelveNavigation = {"var":"value"};';
		$minfiedJs = 'var twentytwelveNavigation={"var":"value"};';

		$this->assertSame( $rawJs, $wp_scripts->registered['twentytwelve-navigation']->extra['data'] );
		$this->assertNotContains( $minfiedJs, $header );
		$this->assertContains( $minfiedJs, $footer );
	}

	public function testEnqueueScriptDependencies() {
		global $wp_scripts;

		wp_enqueue_script( 'twentytwelve-navigation', get_template_directory_uri() . '/js/navigation.js', array( 'jquery' ), '1.0', true );

		$this->plugin->extractScripts();

		$this->assertContains( 'jquery', array_keys( $wp_scripts->done ) );
	}

	public function testEnqueueStyleDependencies() {
		global $wp_styles;

		wp_register_style( 'twentytwelve-style', get_stylesheet_uri() );
		wp_enqueue_style( 'twentytwelve-ie', get_template_directory_uri() . '/css/ie.css', array( 'twentytwelve-style' ), '20121010' );

		$this->plugin->extractStyles();

		$this->assertContains( 'twentytwelve-style', array_keys( $wp_styles->done ) );
	}
}