<?php
include('../wp-load.php');
$querystr = 'SELECT * FROM wp_posts where ID IN ( select post_id from wp_postmeta where meta_key="_wp_page_template" and meta_value="page template/city-page.php" ) and post_content!="" ';
$pageposts = $wpdb->get_results($querystr, OBJECT);
echo "<table>";
foreach($pageposts as $singlepost)
{
	echo '<tr><td>update wp_posts set post_content="'.$meta_value.'" where ID="'.$singlepost->ID.'";</td></tr>';
}
echo '</table>';
?>