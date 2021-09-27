<?php
/**
* Plugin Name: Social Auto Post
* Plugin URI: www.moredeal.co.uk
* Description: This plugin will upload your products and posts on facebook and twitter.
* Author: Estefania
* Author URI: www.moredeal.co.uk
* Version: 0.6.0
* Stable tag: 0.6.0
* License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

set_time_limit(0);
include_once("includes/social-auto-post-function.php");
require_once( 'classes/class-woo-socio.php' );

// FaceBook integrations.
require_once( 'classes/facebook.php' );

global $woosocio;
$woosocio = new Woo_Socio( __FILE__ );
$woosocio->version = '0.6.0';
$woosocio->init();

register_activation_hook(__FILE__,'activation');
function activation () 
{
	//$this->register_plugin_version();
	global $wpdb;
	$sql="CREATE TABLE IF NOT EXISTS postedtosocialmedia (
	`aid` int(11) NOT NULL AUTO_INCREMENT,
	`entityid` int(11) NOT NULL,
	`entitytype` varchar(255) NOT NULL,
	`postedtogmail` tinyint(4) NOT NULL,
	`postedtogmaildate` date NOT NULL,
	`postedtofb` tinyint(4) NOT NULL,
	`postedtofbdate` date NOT NULL,
	`postedtotwitter` tinyint(4) NOT NULL,
	`postedtotwitterdate` date NOT NULL,
	PRIMARY KEY (`aid`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	$wpdb->query($sql);

	$sql="CREATE TABLE IF NOT EXISTS uploadfeed (
	`uploadfeedid` int(11) NOT NULL AUTO_INCREMENT,
	`feedtitle` varchar(255) NOT NULL,
	`originalfilename` varchar(255) NOT NULL,
	`feedfilename` varchar(255) NOT NULL,
	`feeduploadeddate` date NOT NULL,
	`totalproductsinfeed` tinyint(4) NOT NULL,
	`feedstatus` tinyint(4) NOT NULL,
	`uploadedproduct` tinyint(4) NOT NULL,
	PRIMARY KEY (`uploadfeedid`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	$wpdb->query($sql);

	$sql="ALTER TABLE `uploadfeed` CHANGE `feedstatus` `feedstatus` TINYINT( 4 ) NOT NULL COMMENT '0 for not uploaded, 1 for uploaded'";
	$wpdb->query($sql);

	//wp_schedule_event(time(),'sevenamuploadproductdb','uploadproductindbatsevenam');
	//wp_schedule_event(time(),'posttoscialmedia','posted_to_social_media');
}

register_deactivation_hook(__FILE__,'deactivate_plugin');
function deactivate_plugin() 
{
	global $wpdb;
	$sql="drop table postedtosocialmedia";
	//$wpdb->query($sql);

	$removefeeds=mysql_query("select uploadfeedid, feedtitle, originalfilename, feedfilename, feeduploadeddate, totalproductsinfeed, feedstatus, uploadedproduct from uploadfeed order by uploadfeedid desc ");
	if(mysql_num_rows($removefeeds)>0)
	{
		while($resultremovefeeds=mysql_fetch_assoc($removefeeds))
		{
			$upload_dir = wp_upload_dir();
			$basedir=$upload_dir['basedir'];
			$feeddir=$basedir."/uploadfeed";
			@unlink($feeddir."/".$resultremovefeeds['feedfilename']);
		}
	}

	$sql="drop table uploadfeed";
	$wpdb->query($sql);

	wp_clear_scheduled_hook( 'uploadproductindbatsevenam' );
}

add_filter('cron_schedules', 'new_cron_interval');
function new_cron_interval($interval) {
    $interval['sevenamuploadproductdb'] = array('interval'=>24*60*60,'display'=>'Every day at 7 am');
    $interval['posttoscialmedia'] 		= array('interval'=>10*60,'display'=>'Once in a day');
    return $interval;
}

add_action('uploadproductindbatsevenam','upload_product_indb_seven_am');
function upload_product_indb_seven_am() 
{
	global $wpdb;
	$file = fopen(time().".txt","w");

	$feedfile=mysql_query("select * from uploadfeed where feedstatus!=1 order by uploadfeedid desc ");
	$str='';
	if(mysql_num_rows($feedfile)>0)
	{
		while($resultsfeedfile=mysql_fetch_array($feedfile))
		{
			$str.=$resultsfeedfile['feedfilename']." , ".$resultsfeedfile['uploadfeedid'];
			UploadProductFromCsv($resultsfeedfile['feedfilename'],$resultsfeedfile['uploadfeedid']);
		}
	}

	fwrite($file,$str);
	fclose($file);
}

add_action('posted_to_social_media','fn_posted_to_social_media');
function fn_posted_to_social_media() 
{
	$woosocio->PostToFacebook();
	$woosocio->PostToTwitter();
}

?>