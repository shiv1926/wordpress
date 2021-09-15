wordpress site performance : https://wordpress.org/support/article/optimization/
how to create theme in wordpress
create an script for downloading all the images from live site to local machine
create custom search with number pagination
what is amp and amp plugin
how to use and configure CDN , free cdn
SMTP plugin, cconfigure it


create variables in yoast seo plugin to dynamic content
function get_myname() {
    return 'My name is Moses';
}

function register_custom_yoast_variables() {
    wpseo_register_var_replacement( '%%myname%%', 'get_myname', 'advanced', 'some help text' );
}
add_action('wpseo_register_extra_replacements', 'register_custom_yoast_variables');


plugin handbook : https://developer.wordpress.org/plugins/
http://www.billerickson.net/code/default-term-for-taxonomy/
