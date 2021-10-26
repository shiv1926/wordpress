<?php
include_once("../wp-load.php");
$phone = '3955';
echo 'Phone number : '.$phone;
echo "<br>";
$query = " select ID from wp_posts where ID in ( SELECT post_id FROM `wp_postmeta` WHERE (
CONVERT(`meta_key` USING utf8) LIKE '%".$phone."%' OR 
CONVERT(`meta_value` USING utf8) LIKE '%".$phone."%'
) ) and post_status = 'publish' ";

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