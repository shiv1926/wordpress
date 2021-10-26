<?php
include_once("../wp-load.php");
$args = array(
	'taxonomy'      => 'post_tag',
	'hide_empty'       => 0,
);
$posts_array = get_categories($args);
foreach ($posts_array as $key => $value) 
{
	echo get_category_link($value->term_id);
	echo "<br>";
}
?>