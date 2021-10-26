<?php
include_once("../wp-load.php");
$phone = '3955';
echo 'Phone number : '.$phone;
echo "<br>";
$query = "SELECT ID FROM `wp_posts` WHERE (
CONVERT(`post_author` USING utf8) LIKE '%".$phone."%' OR 
CONVERT(`post_content` USING utf8) LIKE '%".$phone."%' OR 
CONVERT(`post_title` USING utf8) LIKE '%".$phone."%' OR 
CONVERT(`post_excerpt` USING utf8) LIKE '%".$phone."%' OR 
CONVERT(`post_status` USING utf8) LIKE '%".$phone."%' OR 
CONVERT(`comment_status` USING utf8) LIKE '%".$phone."%' OR 
CONVERT(`ping_status` USING utf8) LIKE '%".$phone."%' OR 
CONVERT(`post_password` USING utf8) LIKE '%".$phone."%' OR 
CONVERT(`post_name` USING utf8) LIKE '%".$phone."%' OR 
CONVERT(`to_ping` USING utf8) LIKE '%".$phone."%' OR 
CONVERT(`pinged` USING utf8) LIKE '%".$phone."%' OR 
CONVERT(`post_content_filtered` USING utf8) LIKE '%".$phone."%' OR 
CONVERT(`post_parent` USING utf8) LIKE '%".$phone."%' OR 
CONVERT(`post_type` USING utf8) LIKE '%".$phone."%' OR
CONVERT(`post_mime_type` USING utf8) LIKE '%".$phone."%' OR
CONVERT(`comment_count` USING utf8) LIKE '%".$phone."%'
) and post_status = 'publish'";

$citypages = $wpdb->get_results($query);
echo "total pages ".count($citypages);
echo "<table>";
foreach($citypages as $currentpost)
{
	echo "<tr>";
	echo '<td><a href="https://www.carportcentral.com/wp-admin/post.php?post='.$currentpost->ID.'&action=edit" target="_blank">'.$currentpost->ID.'</a></td>';
	echo "</tr>";
}
echo "</table>";
?>