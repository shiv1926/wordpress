<?php
include_once("../wp-load.php");
$args = array(
	'posts_per_page'   => -1,
	'orderby'          => 'date',
	'order'            => 'DESC',
	'post_type'        => 'attachment',
);
$posts_array = get_posts( $args );
$count = 1;
echo '<table border="0">';
foreach ($posts_array as $key => $value) 
{
	echo '<tr>';
	//echo '<td>'.$value->post_title.'</td>';
	echo '<td>'.get_permalink($value->ID).'</td>';
	echo '</tr>';
	$count++;
}
echo '</table>';
echo "Total Images : ".$count;
?>
