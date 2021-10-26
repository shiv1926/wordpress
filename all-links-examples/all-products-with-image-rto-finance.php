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
echo '<td>Title</td><td>?Url</td><td>Image</td><td>Width</td><td>Height</td><td>RTO</td><td>Finance</td>';
echo '</tr>';
foreach ($posts_array as $key => $value) 
{
    $shop_single = wp_get_attachment_image_src(get_post_thumbnail_id($value->ID),'full');
    list($width, $height, $type, $attr) = getimagesize($shop_single[0]);
	echo '<tr>';
	echo '<td>'.$value->post_title.'</td>';
	echo '<td>'.get_permalink($value->ID).'</td>';
	echo '<td><img src="'.$shop_single[0].'" style="width: 200px;"></td>';
	echo '<td>'.$width.'</td>';
	echo '<td>'.$height.'</td>';
	echo '<td>'.($width/$height).'</td>';
	echo '<td>'.get_field('rto',$value->ID).'</td>';
	echo '<td>'.get_field('finance',$value->ID).'</td>';

	echo '</tr>';
}
echo '</table>';
?>