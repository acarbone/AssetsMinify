<?php
namespace AssetsMinify\Assets;

use Assetic\Filter\MinifyCssCompressorFilter;
use Assetic\Filter\CssRewriteFilter;

//use Minify_CSSmin;

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

		// Trigger dependency resolution
		$wp_styles->all_deps($wp_styles->queue);

		foreach( $wp_styles->to_do as $key => $handle ) {

			/*TODO: if ( $this->isFileExcluded($wp_styles->registered[$handle]->src) )
				continue;*/

			//Removes absolute part of the path if it's specified in the src
			$style_path = $this->guessPath($wp_styles->registered[$handle]->src);

			// Script didn't match any case (plugin, theme or wordpress locations)
			if( $style_path == false )
				continue;

			if ( !file_exists($style_path) )
				continue;

			//Separation between preprocessors and css stylesheets
			$ext = 'css';
			$parts = explode('.', $style_path);
			if ( count($parts) > 0 ) {
				$ext = $parts[ count($parts) - 1 ];
			}

			$this->assets[$ext]['files'][$handle] = $style_path;
			$this->assets[$ext]['mtimes'][$handle] = filemtime($style_path);

			//Removes css from the queue so this plugin will be
			//responsible to include all the stylesheets except other domains ones.
			$wp_styles->dequeue( $handle );

			//Move the handle to the done array.
			$wp_styles->done[] = $handle;
			unset($wp_styles->to_do[$key]);
		}
	}

	/**
	 * Takes all the SASS stylesheets and manages their queue to asseticize them
	 */
	public function generate() {
		foreach ( $this->assets as $ext => $content ) {
			$mtime = md5( json_encode($content) );
			$cachefile = "$ext-$mtime.css";

			if ( !$this->cache->fs->has( $cachefile ) ) {
				$class = "AssetsMinify\\Assets\\Css\\" . ucfirst($ext);
				new $class( $content['files'], $cachefile, $this );
			}

			$key = "$ext-am-generated";
			$this->files[$key] = $this->cache->getPath() . $cachefile;
			$this->mtimes[$key] = filemtime($this->files[$key]);
		}

		if ( empty($this->files) )
			return false;

		$mtime = md5( json_encode($this->mtimes) );

		//Saves the asseticized stylesheets
		if ( !$this->cache->fs->has( "head-{$mtime}.css" ) ) {
			$cssDump = str_replace('../', '/', $this->createAsset( $this->files, $this->getFilters() )->dump() );
			$cssDump = str_replace( 'url(/wp-', 'url(' . site_url() . '/wp-', $cssDump );
			$cssDump = str_replace( 'url("/wp-', 'url("' . site_url() . '/wp-', $cssDump );
			$cssDump = str_replace( "url('/wp-", "url('" . site_url() . "/wp-", $cssDump );
			$this->cache->fs->set( "head-{$mtime}.css", $cssDump );
		}

		//Prints css inclusion in the page
		$this->dump( "head-{$mtime}.css" );
	}

	/**
	 * Prints <link> tag to include the CSS
	 */
	protected function dump( $filename ) {
		echo "<link href='" . $this->cache->getUrl() . $filename . "' media='screen, projection' rel='stylesheet' type='text/css'>";
	}
}