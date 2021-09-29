<?php 
/* Plugin Name:  Dealer Location
Plugin URI: https://liugongindia.com/
Description: Get dropdown of countries, states and cities.
Version: 1.0 
Author URI: https://liugongindia.com/
Author: Shivkumar yadav
License: GPL2 
*/ 

function dealer_location_activation() { 
    global $wpdb;
    $sql1 = 'CREATE TABLE IF NOT EXISTS `wp_dealer_cities` (
      `city_id` int(11) NOT NULL AUTO_INCREMENT,
      `city_name` varchar(255) NOT NULL,
      `state_id` int(11) NOT NULL,
      PRIMARY KEY (`city_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;';
    $wpdb->query($sql1);

    $sql2 = 'CREATE TABLE IF NOT EXISTS `wp_dealer_countries` (
      `country_id` int(11) NOT NULL AUTO_INCREMENT,
      `country_name` varchar(255) NOT NULL,
      PRIMARY KEY (`country_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;';
    $wpdb->query($sql2);

    $sql3 = 'CREATE TABLE IF NOT EXISTS `wp_dealer_states` (
      `state_id` int(11) NOT NULL AUTO_INCREMENT,
      `state_name` varchar(255) NOT NULL,
      `country_id` int(11) NOT NULL,
      PRIMARY KEY (`state_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;';
    $wpdb->query($sql3);
}
register_activation_hook(__FILE__, 'dealer_location_activation');

function dealer_location_deactivation() { 
    global $wpdb;
    $sql1 = 'DROP TABLE `wp_dealer_cities`;';
    $wpdb->query($sql1);
    $sql2 = 'DROP TABLE `wp_dealer_countries`;';
    $wpdb->query($sql2);
    $sql3 = 'DROP TABLE `wp_dealer_states`;';
    $wpdb->query($sql3);
}
register_deactivation_hook(__FILE__, 'dealer_location_deactivation'); 

function dealer_location_menu() {
    add_menu_page('Dealer Locations', 'Locations', 'administrator', 'dealer-locations', 'dealer_location','',111);
    add_submenu_page( 'dealer-locations', 'Countries Dropdown','Countries', 'administrator', 'countries', 'countries',1);
    add_submenu_page( 'dealer-locations', 'States Dropdown','States', 'administrator', 'states', 'states',2);
    add_submenu_page( 'dealer-locations', 'Cities Dropdown','Cities', 'administrator', 'cities', 'cities',3);
}
add_action( 'admin_menu', 'dealer_location_menu' );

if(!function_exists('dealer_location')){    
    function dealer_location(){
        include_once "inc/countries.php";
    }
}

if(!function_exists('countries')){    
    function countries(){
        include_once "inc/countries.php";
    }
}

if(!function_exists('states')){    
    function states(){
        include_once "inc/states.php";
    }
}

if(!function_exists('cities')){    
    function cities(){
        include_once "inc/cities.php";
    }
}

add_action('init', 'register_script');
function register_script() {
    wp_register_script('dealer_location_js', plugins_url('/js/dealer_location.js', __FILE__));
    wp_register_style('dealer_location_css', plugins_url('/css/dealer_location.css', __FILE__));
}


add_action('admin_enqueue_scripts', 'enqueue_style');
function enqueue_style(){
   wp_enqueue_script('dealer_location_js');
   wp_enqueue_style('dealer_location_css');
}

function get_country_name($cid)
{
    global $wpdb;
    $sql = "SELECT * FROM ".$wpdb->prefix."dealer_countries where country_id='".$cid."' limit 0,1 ";
    $results = $wpdb->get_results($sql);
    return $results[0]->country_name;
}

add_action('wp_ajax_add_country','add_country');
add_action('wp_ajax_nopriv_add_country','add_country');
function add_country()
{
    global $wpdb;
    $return = array();
    $country_name = sanitize_text_field(trim($_REQUEST['country_name']));
    if($country_name!='') 
    {
        $table_name = $wpdb->prefix."dealer_countries";
        $sql = "SELECT * FROM ".$table_name." where country_name='".$country_name."'";
        $results = $wpdb->get_results($sql);
        if(count($results) > 0) {
            $return['status'] = 'error';
            $return['message'] = 'already';
        } else {
            $wpdb->insert($table_name, array('country_name' => $country_name));
            $return['status'] = 'success';
            $return['message'] = 'success';
        }
    }
    else
    {
        $return['status'] = 'error';
        $return['message'] = 'empty';
    }
    echo json_encode($return);
    wp_die();
}

add_action('wp_ajax_add_state','add_state');
add_action('wp_ajax_nopriv_add_state','add_state');
function add_state()
{
    global $wpdb;
    $return = array();
    $country_name = sanitize_text_field(trim($_REQUEST['country_name']));
    $state_name = sanitize_text_field(trim($_REQUEST['state_name']));
    $flag = TRUE;

    if($country_name=='')
    {
        $flag = FALSE;
        $return['status'] = 'error';
        $return['err_obj'][] = array('field'=>'country_message', 'message'=>'Country field can not be empty.');
    }
    else
    {
        $return['err_obj'][] = array('field'=>'country_message', 'message'=>'&nbsp;');
    }

    if($state_name=='')
    {
        $flag = FALSE;
        $return['status'] = 'error';
        $return['err_obj'][] = array('field'=>'state_message', 'message'=>'State field can not be empty.');
    }
    else
    {
        $return['err_obj'][] = array('field'=>'state_message', 'message'=>'&nbsp;');
    }

    if($flag==TRUE)
    {
        unset($return['err_obj']);
        $table_name = $wpdb->prefix."dealer_states";
        $sql = "SELECT * FROM ".$table_name." where country_id='".$country_name."' and state_name='".$state_name."'";
        $results = $wpdb->get_results($sql);
        if(count($results) > 0) {
            $return['status'] = 'error';
            $return['err_obj'][] = array('field'=>'state_message', 'message'=>'State already exist.');
        } else {
            $wpdb->insert($table_name, array('country_id' => $country_name, 'state_name'=>$state_name));
            $return['status'] = 'success';
        }
    }
    echo json_encode($return);
    wp_die();
}

add_action('wp_ajax_add_city','add_city');
add_action('wp_ajax_nopriv_add_city','add_city');
function add_city()
{
    global $wpdb;
    $return = array();
    $country_name = sanitize_text_field(trim($_REQUEST['country_name']));
    $state_name = sanitize_text_field(trim($_REQUEST['state_name']));
    $city_name = sanitize_text_field(trim($_REQUEST['city_name']));
    $flag = TRUE;

    if($country_name=='')
    {
        $flag = FALSE;
        $return['status'] = 'error';
        $return['err_obj'][] = array('field'=>'country_message', 'message'=>'Required.');
    }
    else
    {
        $return['err_obj'][] = array('field'=>'country_message', 'message'=>'&nbsp;');
    }

    if($state_name=='')
    {
        $flag = FALSE;
        $return['status'] = 'error';
        $return['err_obj'][] = array('field'=>'state_message', 'message'=>'Required.');
    }
    else
    {
        $return['err_obj'][] = array('field'=>'state_message', 'message'=>'&nbsp;');
    }

    if($city_name=='')
    {
        $flag = FALSE;
        $return['status'] = 'error';
        $return['err_obj'][] = array('field'=>'city_message', 'message'=>'Required.');
    }
    else
    {
        $return['err_obj'][] = array('field'=>'city_message', 'message'=>'&nbsp;');
    }

    if($flag==TRUE)
    {
        unset($return['err_obj']);
        $table_name = $wpdb->prefix."dealer_cities";
        $sql = "SELECT * FROM ".$table_name." where state_id='".$state_name."' and city_name='".$city_name."'";
        $results = $wpdb->get_results($sql);
        if(count($results) > 0) {
            $return['status'] = 'error';
            $return['err_obj'][] = array('field'=>'city_message', 'message'=>'City already exist.');
        } else {
            $wpdb->insert($table_name, array('state_id' => $state_name, 'city_name'=>$city_name));
            $return['status'] = 'success';
        }
    }
    echo json_encode($return);
    wp_die();
}

add_action('wp_ajax_get_states_by_country','get_states_by_country');
add_action('wp_ajax_nopriv_get_states_by_country','get_states_by_country');
function get_states_by_country()
{
    global $wpdb;
    $return = array();
    $country_id = sanitize_text_field(trim($_REQUEST['country_id']));
    $sql = "SELECT * FROM ".$wpdb->prefix."dealer_states where country_id='".$country_id."' order by state_name asc";
    $results = $wpdb->get_results($sql);
    $option = '';
    if(count($results) > 0) 
    {
        foreach( $results as $result)
        {
            $option.='<option value="'.$result->state_id.'">'.$result->state_name.'</option>';
        }
    }
    else
    {
        $option.='<option value="">Select State</option>';
    }
    $return['status'] = 'success';
    $return['opt_list'] = $option;
    echo json_encode($return);
    wp_die();
}

add_action('wp_ajax_get_cities_by_state','get_cities_by_state');
add_action('wp_ajax_nopriv_get_cities_by_state','get_cities_by_state');
function get_cities_by_state()
{
    global $wpdb;
    $return = array();
    $state_id = sanitize_text_field(trim($_REQUEST['state_id']));
    $sql = "SELECT * FROM ".$wpdb->prefix."dealer_cities where state_id='".$state_id."' order by city_name asc";
    $results = $wpdb->get_results($sql);
    $option = '';
    if(count($results) > 0) 
    {
        foreach( $results as $result)
        {
            $option.='<option value="'.$result->city_id.'">'.$result->city_name.'</option>';
        }
    }
    else
    {
        $option.='<option value="">Select City</option>';
    }
    $return['status'] = 'success';
    $return['opt_list'] = $option;
    echo json_encode($return);
    wp_die();
}

add_action( 'add_meta_boxes', 'add_dealer_metaboxes' );
function add_dealer_metaboxes() {
    add_meta_box(
        'wp_dealer_location',
        'Dealer Location',
        'wp_dealer_location',
        'dealer',
        'normal',
        'default'
    );
}

function wp_dealer_location() {
    $return='';
    global $wpdb;
    $sql = "SELECT * FROM ".$wpdb->prefix."dealer_countries order by country_name asc ";
    $results = $wpdb->get_results($sql);
    $option = '';
    foreach( $results as $result)
    {
        $option.='<option value="'.$result->country_id.'">'.$result->country_name.'</option>';
    }
    $return.='<div class="horizontal_form choose_location">';
    $return.='<div class="fields">';
    $return.='<label for="parent">Country</label>';
    $return.='<select name="country_name" id="country_name" onclick="get_states_by_country(this);" class="required">';
    $return.='<option value="">Select Country</option>';
    $return.=$option;
    $return.='</select>';
    $return.='</div>';
    $return.='<div class="fields">';
    $return.='<label for="parent">State</label>';
    $return.='<select name="state_name" id="state_name" onclick="get_cities_by_state(this);" class="required">';
    $return.='<option value="">Select State</option>';
    $return.='</select>';
    $return.='</div>';
    $return.='<div class="fields">';
    $return.='<label for="parent">City</label>';
    $return.='<select name="city_name" id="city_name" class="required">';
    $return.='<option value="">Select City</option>';
    $return.='</select>';
    $return.='</div>';
    $return.='</div>';
    echo $return;
}

add_action('save_post', 'save_dealer_location');
function save_dealer_location($post_id) {
    if( !current_user_can( 'edit_post' ) ) return;
    update_post_meta($post_id, 'dealer_country_id',$_POST['country_name']);
    update_post_meta($post_id, 'dealer_state_id',$_POST['state_name']);
    update_post_meta($post_id, 'dealer_city_id',$_POST['city_name']);
}