<?php
include_once("../wp-load.php");
$args = array(
	'posts_per_page'   => -1,
	'orderby'          => 'date',
	'order'            => 'DESC',
	'post_type'        => 'post',
	'post_status'      => 'publish',
);
$posts_array = get_posts( $args );
echo '<table border="0">';
foreach ($posts_array as $key => $value) 
{
	echo '<tr>';
	echo '<td>'.$value->post_title.'</td><td>'.get_permalink($value->ID).'</td>';
	echo '</tr>';
}
echo '</table>';
?>
