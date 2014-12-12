<?php
namespace AssetsMinify\Assets;

use Assetic\Filter\JSMinFilter;

class Js extends Factory {

	public function setFilters() {
		$this->setFilter('JSMin', new JSMinFilter);
	}

	/**
	 * Takes all the scripts enqueued to the theme and removes them from the queue
	 */
	public function extract() {
		global $wp_scripts;

		if ( empty($wp_scripts->queue) )
			return;

		// Trigger dependency resolution
		$wp_scripts->all_deps($wp_scripts->queue);

		foreach( $wp_scripts->to_do as $key => $handle ) {

			/*TODO:if ( $this->isFileExcluded($wp_scripts->registered[$handle]->src) )
				continue;*/

			$script_path = $this->guessPath($wp_scripts->registered[$handle]->src);

			// Script didn't match any case (plugin, theme or wordpress locations)
			if( $script_path === false )
				continue;

			$where = 'footer';
			//Unfortunately not every WP plugin developer is a JS ninja
			//So... let's put it in the header.
			if ( empty($wp_scripts->registered[$handle]->extra) && empty($wp_scripts->registered[$handle]->args) )
				$where = 'header';

			if ( empty($script_path) || !is_file($script_path) )
				continue;

			//Separation between css-frameworks stylesheets and .css stylesheets
			$ext = substr( $script_path, -7 );

			if ( $ext === '.coffee' ) {
				$this->coffee[ $where ][ $handle ] = $script_path;
				$this->mTimesCoffee[ $where ][ $handle ]  = filemtime( $this->coffee[ $where ][ $handle ] );
			} else {
				$this->scripts[ $where ][ $handle ] = $script_path;
				$this->mTimes[ $where ][ $handle ]  = filemtime( $this->scripts[ $where ][ $handle ] );
			}

			//Removes scripts from the queue so this plugin will be
			//responsible to include all the scripts except other domains ones.
			$wp_scripts->dequeue( $handle );

			//Move the handle to the done array.
			$wp_scripts->done[] = $handle;
			unset($wp_scripts->to_do[$key]);
		}

	}
}