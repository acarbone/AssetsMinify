=== AssetsMinify ===
Contributors: ale.carbo
Donate link: 
Tags: assets, minify, css, js, less, sass, compass
Requires at least: 3.3
Tested up to: 3.5.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Stable tag: 1.0.1

AssetsMinify is a WordPress plugin based on Assetic library to minify JS and CSS assets and compile sass/less stylesheets.

== Description ==

How many times have you wished to minify in a clean way all the stylesheets and scripts of a WordPress website?

AssetsMinify takes every CSS and JS asset included using `wp_enqueue_style()` and `wp_enqueue_script()` and Merges+Minifies them.

You can also use AssetsMinify to create your WP theme using Compass / sass / less without configuring any `config.rb` or *that kind of stuff*.

[Fork me on Github](https://github.com/acarbone/AssetsMinify).

== Installation ==

1. Upload the `assets-minify` folder to the `/wp-content/plugins/` directory
1. Activate the AssetsMinify plugin through the 'Plugins' menu in WordPress
1. Set write permission to uploads directory. In most cases: chmod 777 wp-content/uploads/
1. Configure the plugin by going to the `Settings > AssetsMinify` menu that appears in your admin menu: you can choose whether to use Compass to compile sass files or not flagging "Use Compass" field. If you check the flag "Use Compass" you can also specify the Compass compiler's path ( default is /usr/bin/compass ).
1. Important! If you choose to use Compass, the [PHP proc_open function](http://php.net/manual/en/function.proc-open.php) has to be enabled from the server on which the website relies.

== Frequently asked questions ==



== Screenshots ==

1. AssetsMinify's configuration panel
2. How to include your stylesheets
3. Set 777 permissions to you uploads directory

== Changelog ==

= 1.0.1 =

* Fixed bug that caused PHP Warning if CSS/JS file doesn't exist
* Extended compatibility to PHP 5.3


== Upgrade notice ==
