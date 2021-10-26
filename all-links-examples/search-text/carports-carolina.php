<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title></title>
	<link rel="stylesheet" href="">
	<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
</head>
<body>
<?php
/* all customfields, meta data, are in post_meta table,  */


include_once("../../wp-load.php");
$query = "SELECT distinct(ID) FROM `carportcentral`.`wp_posts` WHERE (CONVERT(`ID` USING utf8) LIKE '%carports carolina%' OR CONVERT(`post_author` USING utf8) LIKE '%carports carolina%' OR CONVERT(`post_date` USING utf8) LIKE '%carports carolina%' OR CONVERT(`post_date_gmt` USING utf8) LIKE '%carports carolina%' OR CONVERT(`post_content` USING utf8) LIKE '%carports carolina%' OR CONVERT(`post_title` USING utf8) LIKE '%carports carolina%' OR CONVERT(`post_excerpt` USING utf8) LIKE '%carports carolina%' OR CONVERT(`post_status` USING utf8) LIKE '%carports carolina%' OR CONVERT(`comment_status` USING utf8) LIKE '%carports carolina%' OR CONVERT(`ping_status` USING utf8) LIKE '%carports carolina%' OR CONVERT(`post_password` USING utf8) LIKE '%carports carolina%' OR CONVERT(`post_name` USING utf8) LIKE '%carports carolina%' OR CONVERT(`to_ping` USING utf8) LIKE '%carports carolina%' OR CONVERT(`pinged` USING utf8) LIKE '%carports carolina%' OR CONVERT(`post_modified` USING utf8) LIKE '%carports carolina%' OR CONVERT(`post_modified_gmt` USING utf8) LIKE '%carports carolina%' OR CONVERT(`post_content_filtered` USING utf8) LIKE '%carports carolina%' OR CONVERT(`post_parent` USING utf8) LIKE '%carports carolina%' OR CONVERT(`guid` USING utf8) LIKE '%carports carolina%' OR CONVERT(`menu_order` USING utf8) LIKE '%carports carolina%' OR CONVERT(`post_type` USING utf8) LIKE '%carports carolina%' OR CONVERT(`post_mime_type` USING utf8) LIKE '%carports carolina%' OR CONVERT(`comment_count` USING utf8) LIKE '%carports carolina%')  and post_status='publish' ORDER BY `wp_posts`.`post_status`  DESC";
	$pages = $wpdb->get_results($query);
	echo '<table border="1" cellpadding="5" cellspacing="0">';
	foreach($pages as $currentpost)
	{
		echo '<tr>';
		echo '<td>'.$currentpost->ID.'</td>';
		echo '<td><a href="https://www.carportcentral.com/wp-admin/post.php?post='.$currentpost->ID.'&action=edit" target="_blank">'.get_permalink($currentpost->ID).'</a></td>';
		echo '</tr>';
	}

	$query='';$pages='';

	$query = "select ID from `carportcentral`.`wp_posts` where ID in ( SELECT distinct(post_id) FROM `carportcentral`.`wp_postmeta` WHERE (CONVERT(`meta_id` USING utf8) LIKE '%carports carolina%' OR CONVERT(`post_id` USING utf8) LIKE '%carports carolina%' OR CONVERT(`meta_key` USING utf8) LIKE '%carports carolina%' OR CONVERT(`meta_value` USING utf8) LIKE '%carports carolina%') ) and post_status='publish'";
		$pages = $wpdb->get_results($query);
	echo '<table border="1" cellpadding="5" cellspacing="0">';
	foreach($pages as $currentpost)
	{
		echo '<tr>';
		echo '<td>'.$currentpost->ID.'</td>';
		echo '<td><a href="https://www.carportcentral.com/wp-admin/post.php?post='.$currentpost->ID.'&action=edit" target="_blank">'.get_permalink($currentpost->ID).'</a></td>';
		echo '</tr>';
	}

	echo '</table>';
?>
</body>
</html>