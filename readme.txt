=== AssetsMinify ===
Contributors: ale.carbo
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=USTVFTWRP6DGW
Tags: assets, minify, css, js, less, sass, compass, coffeescript
Requires at least: 3.3
Tested up to: 4.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Stable tag: 2.0.2

Use Compass, Sass, Less, Stylus, CoffeeScript (and more ...) to develop your themes and minify your stylesheets and JavaScript.

== Description ==

How many times have you wished to minify in a clean way all the stylesheets and scripts of a WordPress website?

AssetsMinify takes every CSS and JS asset included using `wp_enqueue_style()` and `wp_enqueue_script()`, merges and minifies them.

You can also use AssetsMinify to create your WP theme using Compass / Sass / Less without configuring any `config.rb` or *that kind of stuff*.

AssetsMinify is based on Assetic library.

[Fork me on Github](https://github.com/acarbone/AssetsMinify).

== Installation ==

1. Upload the `assetsminify` folder to the `/wp-content/plugins/` directory
1. Activate the AssetsMinify plugin through the 'Plugins' menu within WordPress
1. Set write permission to uploads directory. In most cases: chmod 777 wp-content/uploads/
1. Configure the plugin by going to the `Settings > AssetsMinify` menu that appears in your admin menu: you can choose whether to use Compass to compile Sass files or not flagging "Use Compass" field. If you check the flag "Use Compass" you can also specify the Compass compiler's path ( default is /usr/bin/compass ).
1. Important! If you choose to use Compass, the [PHP proc_open function](http://php.net/manual/en/function.proc-open.php) has to be enabled from the server which the website relies on.

== Frequently asked questions ==

= Which version of PHP is needed to use AssetsMinify on my WordPress installation? =

PHP 5.3+

= How can I exclude only certain resources from minification? =

You can fill in the text field "Resources to exclude" within the admin page of the plugin using the filename of the resource. For example: script.js (not the whole path).

== Screenshots ==

1. AssetsMinify's configuration panel
2. How to include your stylesheets
3. Set 777 permissions to you uploads directory

== Changelog ==

= 2.0.2 =

* Bugfixing
* Better compatibility with composer's WordPress installation

= 2.0.1 =

* Fixed bug for minifying js assets

= 2.0.0 =

* Dependency management with composer for external libraries
* Manage Travis CI integration
* Reorganize the Init class to be splitted in more classes
* Rewrite unit-tests and write code using TDD
* [Built-in scripts should also be included](https://wordpress.org/support/topic/built-in-scripts-that-should-be-enqueued-in-footer-are-enqueued-in-header)
* Test on SSL
* [Sass Placeholder support](https://wordpress.org/support/topic/scss-compiler-chokes-on-placeholder-selectors)
* Enable Sass, not only Scss format
* [Allow different media types](https://wordpress.org/support/topic/media-type)
* [Allow to exclude css/js](https://github.com/acarbone/AssetsMinify/issues/21)
* Updated JSMin to patchwork/jsqueeze
* Compile log
* [Add a flag within the admin page for Development Mode. If this mode is active every reload will check if every scss, also imported ones, have been modified.](https://wordpress.org/support/topic/doesnt-detect-change-if-using-manifest-file?replies=1)
* [WooCommerce compatibility](https://github.com/acarbone/AssetsMinify/issues/25)
* [Composer dependency management](https://github.com/acarbone/AssetsMinify/pull/27)

= 1.2.3 =

* SSL and keyframes bugfix thanks @pepe - http://mundschenk.at/

= 1.2.2 =

* Better instructions on AssetsMinify's admin panel
* Defined optional async attribute setting within admin panel
* Check for WordPress 3.8 compatibility

= 1.2.1 =

* Fixed bug of incorrect resources' inclusion path for subdirectory WordPress installations

= 1.2.0 =

* Check which resources you want to exclude from minification
* CoffeeScript compatibility
* New updates for better compatibility with other plugins
* Better check for background images within stylesheets minified

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
