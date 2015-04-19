AssetsMinify
============

[![Build Status](https://travis-ci.org/acarbone/AssetsMinify.svg?branch=dev)](https://travis-ci.org/acarbone/AssetsMinify)
[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/acarbone/assetsminify/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

AssetsMinify is a [WordPress plugin](http://wordpress.org/extend/plugins/assetsminify/) based on [Assetic library](https://github.com/kriswallsmith/assetic) to let using Compass, Sass, Less, CoffeeScript (and more ...) for developing themes and for minifying JS and CSS resources.


Why use it
-------------

How many times have you wished to minify in a clean way all the stylesheets and scripts of a WordPress website? <br>
WordPress offers the way to include JS specifying where to import the script ( within `<head>` or before `</body>` ). <br>
It's good practice include JS before `</body>` for better performances, but not every WordPress plugin's developer is prone to do so.

AssetsMinify takes every CSS and JS asset included using `wp_enqueue_style()` and `wp_enqueue_script()`, merges and minifies them.

You can also use AssetsMinify to create your WP theme using Compass / Sass / Less without configuring any `config.rb` or *that kind of stuff*.


How it works
-------------

You can simply include your stylesheets using the WordPress way:

``` php
<?php
wp_enqueue_style( 'screen',  get_template_directory_uri() . '/css/screen.css' );
wp_enqueue_style( 'home',    get_template_directory_uri() . '/sass/home.scss' );
wp_enqueue_style( 'home-sass',    get_template_directory_uri() . '/sass/home.sass' );
wp_enqueue_style( 'content', get_template_directory_uri() . '/less/content.less' );
```
As you can see I have included three different type of stylesheets: CSS / SCSS / SASS / LESS. <br>
This will work! AssetsMinify will compile 'em all and will combine them in a single css file.

``` php
<?php
wp_enqueue_script( 'script1', get_template_directory_uri() . '/js/script1.js', array(), '1.0', false );
wp_enqueue_script( 'script2', get_template_directory_uri() . '/js/script2.js', array(), '1.0', true );
wp_enqueue_script( 'script3', get_template_directory_uri() . '/js/script3.coffee', array(), '1.0', true );
wp_enqueue_script( 'script4', '//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js', array(), '1.0', true );
```
AssetsMinify detects which are the scripts that would go within the `<head>` ( in the previous sample only script1 ) and which would go before `</body>` ( script2 and script3 ).
External scripts are not managed by AssetsMinify (so script4 in the sample will be included with a separate `<script>` ).

Although it is not a best practice you can define resources inclusions basing on the WordPress page just like this:

``` php
<?php
if ( is_page( 2 ) ) {
	wp_enqueue_style( 'stylesheet-name' );
}
```

Configuration
-------------

AssetsMinify configuration steps are extremely simple.

1.  Set write permission to [uploads directory](http://codex.wordpress.org/Function_Reference/wp_upload_dir). In most cases: chmod 777 wp-content/uploads/
2.  In the admin panel ( Settings > AssetsMinify ) you can choose whether to use Compass to compile Sass files or not flagging "Use Compass" field.
3.  If you check the flag "Use Compass" you can also specify the Compass compiler's path ( default is /usr/bin/compass ).
4.  Important! If you choose to use Compass, the [PHP proc_open function](http://php.net/manual/en/function.proc-open.php) has to be enabled from the server on which the website relies.