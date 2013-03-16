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
As you can see I have included three different type of stylesheets: css / scss / less. <br>
This will work! AssetsMinify will compile 'em all and will combine them in a single css file.

``` php
<?php
wp_enqueue_script( 'script1', get_template_directory_uri() . '/js/script1.js', array(), '1.0', false );
wp_enqueue_script( 'script2', get_template_directory_uri() . '/js/script2.js', array(), '1.0', true );
wp_enqueue_script( 'script3', get_template_directory_uri() . '/js/script3.js', array(), '1.0', true );
wp_enqueue_script( 'script4', '//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js', array(), '1.0', true );
```
AssetsMinify detects which are the scripts that would go within the `<head>` ( in the previous sample only script1 ) and which would go before `</body>` ( script2 and script3 ).
External scripts are not managed by AssetsMinify (so script4 in the sample will be included with a separate `<script>` ).


Configuration
-------------

AssetsMinify configuration steps are extremely simple.

1.  Set write permission to [uploads directory](http://codex.wordpress.org/Function_Reference/wp_upload_dir). In most cases: chmod 777 wp-content/uploads/
2.  In the admin panel ( Settings > AssetsMinify ) you can choose whether to use Compass to compile sass files or not flagging "Use Compass" field.
3.  If you check the flag "Use Compass" you can also specify the Compass compiler's path ( default is /usr/bin/compass ).

Did you like my plugin?
-------------

If you enjoyed my plugin you could offer me a beer!
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="FBXNQXD9X3GZY">
<input type="image" src="https://www.paypalobjects.com/it_IT/IT/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - Il metodo rapido, affidabile e innovativo per pagare e farsi pagare.">
<img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
</form>

