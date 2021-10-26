<?php
echo $text = 'Free Delivery and Installation';
echo "<br>";
include_once("../wp-load.php");
$query = "SELECT a.*, b.* FROM wp_postmeta a, wp_posts b WHERE a.meta_value LIKE '%".$text."%' and a.post_id = b.ID and b.post_status = 'publish'";
//$query = "SELECT * FROM wp_postmeta WHERE meta_value LIKE '%".$text."%'";
$citypages = $wpdb->get_results($query);
echo "<table>";
foreach($citypages as $currentpost)
{
	echo "<tr>";
	echo '<td><a href="'.get_permalink($currentpost->post_id).'" target="_blank">'.get_permalink($currentpost->post_id).'</a></td>';
	echo "</tr>";
}
echo "</table>";
?>