=== AssetsMinify ===
Contributors: ale.carbo
Donate link: 
Tags: assets, minify, css, js, less, sass, compass
Requires at least: 3.3
Tested up to: 3.5.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Stable tag: 1.1.4

Use Compass, SASS and LESS to develop your themes and minify your stylesheets and JavaScript simply by installing AssetsMinify.

== Description ==

How many times have you wished to minify in a clean way all the stylesheets and scripts of a WordPress website?

AssetsMinify takes every CSS and JS asset included using `wp_enqueue_style()` and `wp_enqueue_script()` and Merges+Minifies them.

You can also use AssetsMinify to create your WP theme using Compass / SASS / LESS without configuring any `config.rb` or *that kind of stuff*.

AssetsMinify is based on Assetic library.

This plugin has been tested up to WordPress 3.6 beta.

[Fork me on Github](https://github.com/acarbone/AssetsMinify).

= Define inclusion-sets per-page =

Although it is not a best practice you can define resources inclusions basing on the WordPress page just like this `if ( is_page( 2 ) ) { wp_enqueue_style( 'stylesheet-name' ); }`.

== Installation ==

1. Upload the `assets-minify` folder to the `/wp-content/plugins/` directory
1. Activate the AssetsMinify plugin through the 'Plugins' menu in WordPress
1. Set write permission to uploads directory. In most cases: chmod 777 wp-content/uploads/
1. Configure the plugin by going to the `Settings > AssetsMinify` menu that appears in your admin menu: you can choose whether to use Compass to compile SASS files or not flagging "Use Compass" field. If you check the flag "Use Compass" you can also specify the Compass compiler's path ( default is /usr/bin/compass ).
1. Important! If you choose to use Compass, the [PHP proc_open function](http://php.net/manual/en/function.proc-open.php) has to be enabled from the server on which the website relies.

== Frequently asked questions ==

= Which version of PHP is needed to use AssetsMinify on my WordPress installation? =

PHP 5.3+


== Screenshots ==

1. AssetsMinify's configuration panel
2. How to include your stylesheets
3. Set 777 permissions to you uploads directory

== Changelog ==

= 1.1.4 =

* Provided compatibility on SSL for https resources inclusion

= 1.1.3 =

* Provided compatibility with subdirectory WordPress installation

= 1.1.2 =

* Provided compatibility with subdirectory WordPress installation
* Fixed bug on js inclusions in WP 3.6

= 1.1.1 =

* Provided compatibility with wp_localize_script()

= 1.1.0 =

* Updated cache system to provide multi-sets of different stylesheets or scripts per-page
* Defined garbage collector for old cache files

= 1.0.1 =

* Fixed bug that caused PHP Warning if CSS/JS file doesn't exist
* Extended compatibility to PHP 5.3


== Upgrade notice ==
