<?php  
/* 
* Plugin Name: Quote Entries
* Plugin URI: http://client.cynexis.com
* Version: 1.1
* Author: Cynexis
* Description: This plugin is used to show quote entries
*/

include_once("includes/quote-entries-function.php");
register_activation_hook(__FILE__,'activate_quote_entries');
register_deactivation_hook(__FILE__,'deactivate_quote_entries');

function activate_quote_entries() {
}

function deactivate_quote_entries() {
}

function enqueue_date_picker(){
      wp_enqueue_script(
		'datepickerjs', 
		plugins_url('quote-entries/js/datepickercall.js'), 
		array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'),
		time(),
		true
	);
	wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
}
add_action('admin_enqueue_scripts','enqueue_date_picker');

function pp_admin_page() 
{
    add_menu_page('Quote', 'Quote','manage_options', 'quotes', 'QuoteEntries','',111);
}
add_action('admin_menu', 'pp_admin_page');

function QuoteEntries()
{
	if($_GET['refrence']!='') {
		include_once("includes/quote-entry-details.php");
	} else {
		include_once("includes/quote-entries.php");
	}
}
?>