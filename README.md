AssetsMinify
============

AssetsMinify is a WordPress plugin based on [Assetic library](https://github.com/kriswallsmith/assetic) to minify JS and CSS assets and compile sass/less stylesheets.


Why use it
-------------

How many times have you wished to minify in a clean way all the stylesheets and scripts of a WordPress website? <br>
WordPress offers the way to include JS specifying where to import the script ( within `<head>` or before `</body>` ). <br>
It's good practice include JS before `</body>` for better performances, but not every WordPress plugin's developer is prone to do so.

AssetsMinify takes every CSS and JS asset included using `wp_enqueue_style()` and `wp_enqueue_script()` and Merges+Minifies them.

You can also use AssetsMinify to create your WP theme using Compass / sass / less without configuring any `config.rb` or *that kind of stuff*.

How it works
-------------

You can simply include your stylesheets using the WordPress way:

``` php
<?php
wp_enqueue_style( 'screen',  get_template_directory_uri() . '/css/screen.css' );
wp_enqueue_style( 'home',    get_template_directory_uri() . '/sass/home.scss' );
wp_enqueue_style( 'content', get_template_directory_uri() . '/less/content.less' );
```
As you can see I have included three different type of stylesheets: css / scss / less.
This will work! AssetsMinify will compile 'em all and will combine them in a single css file.

``` php
<?php
wp_enqueue_script( 'script1', get_template_directory_uri() . '/js/script1.js', array(), '1.0', false );
wp_enqueue_script( 'script2', get_template_directory_uri() . '/js/script2.js', array(), '1.0', true );
wp_enqueue_script( 'script3', get_template_directory_uri() . '/js/script3.js', array(), '1.0', true );
wp_enqueue_script( 'script4', '//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js', array(), '1.0', true );
```
AssetsMinify detects which are the scripts the would go within the `<head>` ( in the previous sample only script1 ) and which would go before `</body>` ( script2 and script3 ).
External scripts are not managed by AssetsMinify (so script4 in the sample will be included with a separate `<script>` ).


Configuration
-------------

Using AssetsMinify is absolutely simple because you have only to install it as every normal WP plugin