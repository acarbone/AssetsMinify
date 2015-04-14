<?php
namespace AssetsMinify\Assets;

use Assetic\Filter\MinifyCssCompressorFilter;
use Assetic\Filter\CssRewriteFilter;

use AssetsMinify\Log;

/**
 * Css Factory.
 * Manages the styles (Css and sass, scss, stylus, less)
 *
 * @author Alessandro Carbone <ale.carbo@gmail.com>
 */
class Css extends Factory {

	protected $assets = array(),
			  $files  = array(),
			  $mtimes = array();

	public function setFilters() {
		$this->setFilter('CssMin', new MinifyCssCompressorFilter)
			 ->setFilter('CssRewrite', new CssRewriteFilter);
	}

	/**
	 * Takes all the stylesheets enqueued to the theme and removes them from the queue
	 */
	public function extract() {
		global $wp_styles;

		if ( empty($wp_styles->queue) )
			return;

		$profiler = array( time() );

		// Trigger dependency resolution
		$wp_styles->all_deps($wp_styles->queue);

		foreach( $wp_styles->to_do as $key => $handle ) {

			if ( $this->manager->isFileExcluded($wp_styles->registered[$handle]->src) )
				continue;

			//Removes absolute part of the path if it's specified in the src
			$style_path = $this->guessPath($wp_styles->registered[$handle]->src);

			// Script didn't match any case (plugin, theme or wordpress locations)
			if( $style_path == false )
				continue;

			if ( !file_exists($style_path) )
				continue;

			//Separation of stylesheets enqueue using different media
			$media = $wp_styles->registered[$handle]->args;
			if ( $media  == '' )
				$media = 'all';

			//Separation between preprocessors and css stylesheets
			$ext = 'css';
			$parts = explode('.', $style_path);
			if ( count($parts) > 0 ) {
				$ext = $parts[ count($parts) - 1 ];
			}

			$this->assets[$media][$ext]['files'][$handle] = $style_path;
			$this->assets[$media][$ext]['mtimes'][$handle] = filemtime($style_path);

			//Removes css from the queue so this plugin will be
			//responsible to include all the stylesheets except other domains ones.
			$wp_styles->dequeue( $handle );

			//Move the handle to the done array.
			$wp_styles->done[] = $handle;
			unset($wp_styles->to_do[$key]);
		}
		$profiler []= time();
		Log::getInstance()->set( 'Css extraction', $profiler );
	}

	/**
	 * Takes all the stylesheets and manages their queue to compress them
	 */
	public function generate() {
		$profiler = array( time() );

		foreach ( $this->assets as $media => $assets ) {
			foreach ( $assets as $ext => $content ) {
				$mtime = md5( json_encode($content) );
				$cachefile = "$media-$ext-$mtime.css";

				if ( !$this->cache->fs->has( $cachefile ) ) {
					$class = "AssetsMinify\\Assets\\Css\\" . ucfirst($ext);
					new $class( $content['files'], $cachefile, $this );
				}

				$key = "$media-$ext-am-generated";
				$this->files[$media][$key] = $this->cache->getPath() . $cachefile;
				$this->mtimes[$media][$key] = filemtime($this->files[$media][$key]);
			}
		}

		if ( empty($this->files) )
			return false;

		foreach ( $this->files as $media => $files) {
			$mtime = md5( json_encode($this->mtimes[$media]) );

			//Saves the asseticized stylesheets
			$cachedFilename = "head-$media-$mtime.css";

			if ( !$this->cache->fs->has( $cachedFilename ) ) {
				$cssDump = $this->createAsset( $files, $this->getFilters() )->dump();
				$cssDump = str_replace( 'url(/wp-', 'url(' . site_url() . '/wp-', $cssDump );
				$cssDump = str_replace( 'url("/wp-', 'url("' . site_url() . '/wp-', $cssDump );
				$cssDump = str_replace( "url('/wp-", "url('" . site_url() . "/wp-", $cssDump );
				$this->cache->fs->set( $cachedFilename, $cssDump );
				$this->cache->update();
			}

			//Prints css inclusion in the page
			$this->dump( $cachedFilename, $media );
		}

		$profiler []= time();
		Log::getInstance()->set( 'Css minification', $profiler );
	}

	/**
	 * Prints <link> tag to include the CSS
	 *
	 * @param string $filename The filename to dump
	 * @param string $media The media attribute - Default = all
	 */
	protected function dump( $filename, $media = 'all' ) {
		echo "<link href='" . $this->cache->getUrl() . $filename . "' media='$media' rel='stylesheet' type='text/css'>";
	}
}
