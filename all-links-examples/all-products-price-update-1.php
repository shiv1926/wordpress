<?php
include_once("../wp-load.php");
$args = array(
	'posts_per_page'   => -1,
	'orderby'          => 'date',
	'order'            => 'DESC',
	'post_type'        => 'product',
	'post_status'      => 'publish',
);
$posts_array = get_posts( $args );
echo '<table border="1" cellpadding="5" cellspacing="0">';
echo '<tr>';
echo '<td>ID</td><td>Title</td><td>Edit Url</td><td>View</td><td>Price</td><td>New Price</td>';
echo '</tr>';
foreach ($posts_array as $key => $value) 
{
    $product = wc_get_product($value->ID);
	echo '<tr>';
	echo '<td>'.$value->ID.'</td>';
	echo '<td>'.$value->post_title.'</td>';
	echo '<td><a target="_blank" href="https://garagebuildings.com/wp-admin/post.php?post='.$value->ID.'&action=edit">Edit</a></td>';
	echo '<td><a target="_blank" href="'.get_permalink($value->ID).'">View</a></td>';
	echo '<td>'.$product->get_price().'</td>';
	echo '<td>'.($product->get_price() * 1.2).'</td>';
	echo '</tr>';
}
echo '</table>';
?>