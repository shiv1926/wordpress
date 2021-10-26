<?php
echo $text = 'Free Delivery and Installation';
echo "<br>";
include_once("../wp-load.php");
//$query = "SELECT a.*, b.* FROM wp_postmeta a, wp_posts b WHERE a.meta_value LIKE '%Free Delivery and Installation%' and a.post_id = b.ID and b.post_status = 'publish'";
$query = "SELECT * FROM wp_posts WHERE post_status = 'publish' and ( post_content like '%".$text."%' or post_title like '%".$text."%' or post_excerpt like '%".$text."%' or post_name like '%".$text."%' ) ";
$citypages = $wpdb->get_results($query);
echo "<table>";
foreach($citypages as $currentpost)
{
	echo "<tr>";
	echo '<td><a href="'.get_permalink($currentpost->ID).'" target="_blank">'.get_permalink($currentpost->ID).'</a></td>';
	echo "</tr>";
}
echo "</table>";
?>