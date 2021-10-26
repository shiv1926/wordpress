<?php
include_once("../wp-load.php");
$args = array(
	'taxonomy'      => 'product_cat',
	'hide_empty'       => 0,
);
$posts_array = get_categories($args);
echo '<table border="0">';
foreach ($posts_array as $key => $value) 
{
	echo get_category_link($value->term_id);
	echo "<br>";
}
echo '</table>';
?>
