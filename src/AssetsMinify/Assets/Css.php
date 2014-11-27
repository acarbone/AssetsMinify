<?php
namespace AssetsMinify\Assets;

use Assetic\Filter\MinifyCssCompressorFilter;
use Assetic\Filter\CssRewriteFilter;

class Css extends Factory {

	public function setFilters() {
		$this->asset->setFilter('CssMin', new MinifyCssCompressorFilter)
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

			//Separation between css-frameworks stylesheets and .css stylesheets
			$ext = substr( $style_path, -5 );
			if ( in_array( $ext, array('.sass', '.scss') ) ) {
				$this->sass[ $handle ]       = $style_path;
				$this->mTimesSass[ $handle ] = filemtime($this->sass[ $handle ]);
			} elseif ( $ext == '.less' ) {
				$this->less[ $handle ]       = $style_path;
				$this->mTimesLess[ $handle ] = filemtime($this->less[ $handle ]);
			} else {
				$this->styles[ $handle ]       = $style_path;
				$this->mTimesStyles[ $handle ] = filemtime($this->styles[ $handle ]);
			}

			//Removes css from the queue so this plugin will be
			//responsible to include all the stylesheets except other domains ones.
			$wp_styles->dequeue( $handle );

			//Move the handle to the done array.
			$wp_styles->done[] = $handle;
			unset($wp_styles->to_do[$key]);
		}
	}
}