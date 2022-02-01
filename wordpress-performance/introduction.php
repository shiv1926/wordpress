disable hearbeat : https://onlinemediamasters.com/disable-wordpress-heartbeat-api/
WordPress Heartbeat consumes resources by showing real-time plugin notifications, when other users are editing a post, etc. For most website owners, it usually does more harm than good. There are many plugins to disable this (WP Rocket, Perfmatters, most cache plugins).

To disable the WordPress Heartbeat API without a plugin, go to Appearance > Theme Editor, then edit the functions.php 
add_action( 'init', 'stop_heartbeat', 1 );
function stop_heartbeat() {
wp_deregister_script('heartbeat');
}


8. Don’t Combine CSS + JavaScript
Smaller sites should usually combine CSS/JS while larger sites should not. According to WP Johnny, websites with a CSS/JS size of under 10KB should combine while over 10KB should not. He goes on to say that regarding TTFB, it’s all about starting sooner, not finishing sooner.


10. Increase Memory Limit
Elementor and WooCommerce both require a 256MB memory limit, but you should really increase it to 256MB anyway especially if your website is getting fatal memory limit errors.

Add the code to your wp-config.php before “Happy Blogging.”

define('WP_MEMORY_LIMIT', '256M');

==========================
https://onlinemediamasters.com/reduce-server-response-time-wordpress/

WP Rocket and LiteSpeed Cache are the gold standards for cache plugins.
https://onlinemediamasters.com/wp-rocket-settings/
https://onlinemediamasters.com/wp-fastest-cache-settings/
https://onlinemediamasters.com/w3-total-cache-settings/
https://onlinemediamasters.com/wp-super-cache-settings/

https://www.elegantthemes.com/blog/tips-tricks/how-to-minify-your-websites-css-html-javascript